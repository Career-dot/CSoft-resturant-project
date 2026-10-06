<?php
/**
 * POST /api/taste.php
 * Body: { "answers": { "spice": "hot", "sweet_savory": "sweet", ... } }
 *
 * Scores the submitted answers against config/taste_quiz.php's
 * weights and returns the highest-scoring personality from
 * config/taste_personalities.php. If the person is logged in,
 * the result is also saved to `taste_profiles` (one row per user
 * — a retake overwrites the previous result) so My Taste can show
 * it without retaking the quiz.
 *
 * GET /api/taste.php
 * Returns the logged-in user's previously saved result (or
 * {profile: null} if they haven't taken the quiz yet) — used by
 * pages/my-taste.php.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/taste_quiz.php';
require_once __DIR__ . '/../config/taste_personalities.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return the logged-in user's saved result, if any — used by
    // pages/my-taste.php so it doesn't need to retake the quiz.
    $user = zaiqa_current_user();
    if ($user === null) {
        http_response_code(401);
        echo json_encode(['error' => 'Log in to see your saved taste profile.']);
        exit;
    }
    $pdo = zaiqa_db();
    if ($pdo === null) {
        http_response_code(503);
        echo json_encode(['error' => 'This needs the database connected — see README.md setup steps.']);
        exit;
    }
    try {
        $stmt = $pdo->prepare('SELECT personality_key, personality_name, description, spice_pref, computed_at FROM taste_profiles WHERE user_id = :uid');
        $stmt->execute(['uid' => $user['id']]);
        $row = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('[zaiqa] taste.php GET query failed (has database/step3_taste.sql been imported?): ' . $e->getMessage());
        http_response_code(503);
        echo json_encode(['error' => 'This isn’t set up yet — see README.md setup steps (database/step3_taste.sql).', 'debug' => $e->getMessage()]);
        exit;
    }

    if ($row) {
        // dishes/drink/dining_experience aren't duplicated into the
        // DB row — re-derive them from the same config the quiz
        // scored against, keyed by the saved personality_key.
        $personalities = zaiqa_taste_personalities();
        $full = $personalities[$row['personality_key']] ?? null;
        if ($full) {
            $row['dishes'] = $full['dishes'];
            $row['drink'] = $full['drink'];
            $row['dining_experience'] = $full['dining_experience'];
        }
    }

    echo json_encode(['profile' => $row ?: null]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Use GET or POST.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$answers = is_array($body['answers'] ?? null) ? $body['answers'] : [];

if (empty($answers)) {
    http_response_code(400);
    echo json_encode(['error' => 'No answers submitted.']);
    exit;
}

$result = score_taste_quiz($answers);

if ($result === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Could not score the submitted answers.']);
    exit;
}

$user = zaiqa_current_user();
$saved = false;

if ($user !== null) {
    $pdo = zaiqa_db();
    if ($pdo !== null) {
        try {
            // One profile per user: delete any previous result, then
            // insert the fresh one — simpler than an upsert given
            // taste_profiles has its own surrogate id.
            $pdo->prepare('DELETE FROM taste_profiles WHERE user_id = :uid')->execute(['uid' => $user['id']]);
            $pdo->prepare(
                'INSERT INTO taste_profiles (user_id, personality_key, personality_name, description, spice_pref)
                 VALUES (:uid, :key, :name, :description, :spice_pref)'
            )->execute([
                'uid' => $user['id'],
                'key' => $result['personality_key'],
                'name' => $result['name'],
                'description' => $result['description'],
                'spice_pref' => $result['spice_pref'],
            ]);
            $saved = true;
        } catch (PDOException $e) {
            error_log('[zaiqa] taste profile save failed: ' . $e->getMessage());
        }
    }
}

$result['saved'] = $saved;

echo json_encode($result);

function score_taste_quiz(array $answers): ?array
{
    $questions = zaiqa_taste_questions();
    $personalities = zaiqa_taste_personalities();
    $scores = array_fill_keys(array_keys($personalities), 0);
    $answeredCount = 0;

    foreach ($questions as $question) {
        $chosenOptionId = $answers[$question['id']] ?? null;
        if ($chosenOptionId === null) {
            continue;
        }
        foreach ($question['options'] as $option) {
            if ($option['id'] === $chosenOptionId) {
                foreach ($option['weights'] as $key => $points) {
                    if (isset($scores[$key])) {
                        $scores[$key] += $points;
                    }
                }
                $answeredCount++;
                break;
            }
        }
    }

    if ($answeredCount === 0) {
        return null;
    }

    arsort($scores);
    $topKey = array_key_first($scores);
    $personality = $personalities[$topKey];

    return [
        'personality_key' => $topKey,
        'name' => $personality['name'],
        'description' => $personality['description'],
        'dishes' => $personality['dishes'],
        'drink' => $personality['drink'],
        'spice_pref' => $personality['spice_pref'],
        'dining_experience' => $personality['dining_experience'],
        'scores' => $scores,
        'answered_questions' => $answeredCount,
        'total_questions' => count($questions),
    ];
}
