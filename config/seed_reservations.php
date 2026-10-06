<?php
/**
 * Seed dataset for seating areas + tables — mirrors the seed rows
 * in database/step4_reservations.sql. Used by api/tables.php as a
 * dev-time fallback when MySQL isn't connected (see
 * config/database.php for the pattern).
 */

declare(strict_types=1);

function zaiqa_seed_seating_areas(): array
{
    return [
        ['id' => 1, 'slug' => 'garden', 'name' => 'Garden', 'emoji' => '',
         'description' => 'Open-air seating among string lights and greenery.',
         'atmosphere' => 'Relaxed, breezy, golden-hour perfect.',
         'occasions' => ['Casual dinners', 'Friends gathering'],
         'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=700&q=80'],
        ['id' => 2, 'slug' => 'quiet-corner', 'name' => 'Quiet Corner', 'emoji' => '',
         'description' => 'Tucked-away tables for low-key conversation.',
         'atmosphere' => 'Calm, intimate, softly lit.',
         'occasions' => ['Date night', 'Business dinner'],
         'image' => 'https://images.unsplash.com/photo-1554679665-f5537f187268?auto=format&fit=crop&w=700&q=80'],
        ['id' => 3, 'slug' => 'family-table', 'name' => 'Family Table', 'emoji' => '',
         'description' => 'Wide tables built for sharing and sprawling.',
         'atmosphere' => 'Warm, lively, unhurried.',
         'occasions' => ['Family dinner', 'Celebration'],
         'image' => 'https://images.unsplash.com/photo-1424847651672-bf20a4b0982b?auto=format&fit=crop&w=700&q=80'],
        ['id' => 4, 'slug' => 'music-area', 'name' => 'Music Area', 'emoji' => '',
         'description' => 'Seating near the evening live set.',
         'atmosphere' => 'Energetic, social, a little louder.',
         'occasions' => ['Friends night', 'Birthday'],
         'image' => 'https://images.unsplash.com/photo-1470337458703-46ad1756a187?auto=format&fit=crop&w=700&q=80'],
        ['id' => 5, 'slug' => 'window-seat', 'name' => 'Window Seat', 'emoji' => '',
         'description' => 'Street-facing tables with natural light.',
         'atmosphere' => 'Bright by day, moody by night.',
         'occasions' => ['Casual dinners', 'Solo dining'],
         'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=700&q=80'],
    ];
}

function zaiqa_seed_tables(): array
{
    return [
        ['id' => 1, 'seating_area_id' => 1, 'table_number' => 'T1', 'capacity' => 2, 'grid_row' => 1, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 2, 'seating_area_id' => 1, 'table_number' => 'T2', 'capacity' => 4, 'grid_row' => 1, 'grid_col' => 2, 'status' => 'available'],
        ['id' => 3, 'seating_area_id' => 1, 'table_number' => 'T3', 'capacity' => 6, 'grid_row' => 2, 'grid_col' => 1, 'status' => 'reserved'],
        ['id' => 4, 'seating_area_id' => 2, 'table_number' => 'T4', 'capacity' => 2, 'grid_row' => 1, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 5, 'seating_area_id' => 2, 'table_number' => 'T5', 'capacity' => 2, 'grid_row' => 1, 'grid_col' => 2, 'status' => 'available'],
        ['id' => 6, 'seating_area_id' => 2, 'table_number' => 'T6', 'capacity' => 4, 'grid_row' => 2, 'grid_col' => 1, 'status' => 'unavailable'],
        ['id' => 7, 'seating_area_id' => 3, 'table_number' => 'T7', 'capacity' => 6, 'grid_row' => 1, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 8, 'seating_area_id' => 3, 'table_number' => 'T8', 'capacity' => 8, 'grid_row' => 1, 'grid_col' => 2, 'status' => 'available'],
        ['id' => 9, 'seating_area_id' => 3, 'table_number' => 'T9', 'capacity' => 6, 'grid_row' => 2, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 10, 'seating_area_id' => 4, 'table_number' => 'T10', 'capacity' => 4, 'grid_row' => 1, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 11, 'seating_area_id' => 4, 'table_number' => 'T11', 'capacity' => 4, 'grid_row' => 1, 'grid_col' => 2, 'status' => 'reserved'],
        ['id' => 12, 'seating_area_id' => 4, 'table_number' => 'T12', 'capacity' => 2, 'grid_row' => 2, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 13, 'seating_area_id' => 5, 'table_number' => 'T13', 'capacity' => 2, 'grid_row' => 1, 'grid_col' => 1, 'status' => 'available'],
        ['id' => 14, 'seating_area_id' => 5, 'table_number' => 'T14', 'capacity' => 2, 'grid_row' => 1, 'grid_col' => 2, 'status' => 'available'],
        ['id' => 15, 'seating_area_id' => 5, 'table_number' => 'T15', 'capacity' => 4, 'grid_row' => 2, 'grid_col' => 1, 'status' => 'available'],
    ];
}
