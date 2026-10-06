<?php
/**
 * GET /api/tables.php[?seating_area=garden]
 *
 * Returns seating areas (for the vibe selector) and tables (for
 * the 2D table map), optionally filtered to one area's tables.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_reservations.php';

$areaSlug = trim((string)($_GET['seating_area'] ?? ''));

$data = fetch_tables_data($areaSlug);

echo json_encode($data);

function fetch_tables_data(string $areaSlug): array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
        try {
            $areaStmt = $pdo->query('SELECT id, slug, name, emoji, description, atmosphere, occasions, image_path AS image FROM seating_areas ORDER BY id');
            $areas = $areaStmt->fetchAll();

            if ($areas) {
                foreach ($areas as &$a) {
                    $a['occasions'] = array_filter(explode(',', (string)$a['occasions']));
                }
                unset($a);

                $sql = 'SELECT t.id, t.seating_area_id, sa.slug AS seating_area_slug, t.table_number,
                               t.capacity, t.grid_row, t.grid_col, t.status
                        FROM tables t
                        JOIN seating_areas sa ON sa.id = t.seating_area_id';
                $params = [];
                if ($areaSlug !== '') {
                    $sql .= ' WHERE sa.slug = :slug';
                    $params['slug'] = $areaSlug;
                }
                $sql .= ' ORDER BY t.seating_area_id, t.table_number';

                $tableStmt = $pdo->prepare($sql);
                $tableStmt->execute($params);
                $tables = $tableStmt->fetchAll();

                return ['seating_areas' => $areas, 'tables' => $tables];
            }
        } catch (PDOException $e) {
            // Most likely cause: the DB connects fine, but
            // `seating_areas`/`tables` don't exist yet because
            // database/step4_reservations.sql hasn't been imported.
            // Fall through to seed data instead of a hard 500, so
            // the page still works — but log it clearly since this
            // means reservations won't actually persist until that
            // migration is run.
            error_log('[zaiqa] tables.php: seating_areas/tables query failed (has step4_reservations.sql been imported?): ' . $e->getMessage());
        }
    }

    $areas = zaiqa_seed_seating_areas();
    $tables = zaiqa_seed_tables();

    if ($areaSlug !== '') {
        $area = null;
        foreach ($areas as $a) {
            if ($a['slug'] === $areaSlug) {
                $area = $a;
                break;
            }
        }
        $tables = array_values(array_filter($tables, fn($t) => $area && $t['seating_area_id'] === $area['id']));
        foreach ($tables as &$t) {
            $t['seating_area_slug'] = $areaSlug;
        }
        unset($t);
    } else {
        $slugById = array_column($areas, 'slug', 'id');
        foreach ($tables as &$t) {
            $t['seating_area_slug'] = $slugById[$t['seating_area_id']] ?? null;
        }
        unset($t);
    }

    return ['seating_areas' => $areas, 'tables' => $tables];
}
