<?php
$pageTitle = 'Discover Your Taste | ذائقہ — ZAIQA';
$pageDescription = 'Take the ذائقہ Taste Personality quiz and discover dishes, drinks, and a dining experience matched to your palate.';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/taste_quiz.php';
$questions = zaiqa_taste_questions();
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/discover.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="discover-page">
  <section class="discover-hero">
    <div class="container">
      <p class="discover-hero__eyebrow">The Experience</p>
      <h1>Your mood chooses the first bite.</h1>
      <p class="discover-hero__sub">Answer a few short questions and we'll uncover your taste personality — with dishes, a drink, and a dining vibe to match.</p>
    </div>
  </section>

  <section class="taste-quiz" id="taste-quiz">
    <div class="container taste-quiz__container">

      <div class="taste-quiz__progress-wrap">
        <div class="taste-quiz__progress" id="quizProgress"></div>
      </div>
      <p class="taste-quiz__step-count" id="quizStepCount"></p>

      <form id="tasteQuizForm">
        <?php foreach ($questions as $qi => $question): ?>
          <fieldset class="quiz-step" data-step="<?= $qi ?>" <?= $qi === 0 ? '' : 'hidden' ?>>
            <legend class="quiz-step__question"><?= htmlspecialchars($question['text']) ?></legend>
            <div class="quiz-step__options" role="radiogroup">
              <?php foreach ($question['options'] as $option): ?>
                <label class="quiz-option">
                  <input type="radio" name="<?= htmlspecialchars($question['id']) ?>" value="<?= htmlspecialchars($option['id']) ?>">
                  <span><?= htmlspecialchars($option['label']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </fieldset>
        <?php endforeach; ?>

        <div class="quiz-nav">
          <button type="button" class="btn btn--ghost" id="quizBack" disabled>Back</button>
          <button type="button" class="btn btn--primary" id="quizNext">Next</button>
        </div>
      </form>

      <div class="taste-result" id="tasteResult" hidden>
        <!-- Populated by discover-quiz.js -->
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/discover-quiz.js" defer></script>
