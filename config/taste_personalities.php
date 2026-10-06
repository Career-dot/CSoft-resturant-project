<?php
/**
 * Taste Personality results. Keyed by the same personality keys
 * used as scoring weights in config/taste_quiz.php.
 */

declare(strict_types=1);

function zaiqa_taste_personalities(): array
{
    return [
        'fire' => [
            'name' => 'The Fire Explorer',
            'description' => 'You chase heat and bold spice — the kind of flavor that announces itself. Subtle isn’t really your language.',
            'dishes' => ['Signature Fire Steak', 'Peri Peri Wings'],
            'drink' => 'Spiced Ginger Mocktail',
            'spice_pref' => 'Hot & Bold',
            'dining_experience' => 'Ask for the chili oil on the side — you’ll want it.',
        ],
        'comfort' => [
            'name' => 'The Comfort Seeker',
            'description' => 'Warmth and familiarity, done exceptionally well. You want food that feels like coming home.',
            'dishes' => ['Chicken Karahi', 'Creamy Alfredo Pasta', 'Zaiqa Smash Burger'],
            'drink' => 'Warm Masala Chai',
            'spice_pref' => 'Mild to Medium',
            'dining_experience' => 'The Quiet Corner seating suits you best.',
        ],
        'classic' => [
            'name' => 'The Classic Soul',
            'description' => 'Timeless flavors, done right — you don’t need reinvention when the original is already this good.',
            'dishes' => ['Chicken Karahi', 'Truffle Risotto'],
            'drink' => 'Classic Lemonade',
            'spice_pref' => 'Medium',
            'dining_experience' => 'A Window Seat, watching the evening unfold, feels right.',
        ],
        'sweet' => [
            'name' => 'The Sweet Explorer',
            'description' => 'Dessert-first mindset. You’re always a little bit curious about what’s coming after the main course.',
            'dishes' => ['Molten Lava Cake', 'Kunafa Cheesecake'],
            'drink' => 'Saffron Milk Cooler',
            'spice_pref' => 'Mild',
            'dining_experience' => 'Save room — dessert is the main event tonight.',
        ],
        'fresh' => [
            'name' => 'The Fresh Lover',
            'description' => 'Light, bright, clean flavors that don’t weigh you down. You’d rather leave the table energized than stuffed.',
            'dishes' => ['Citrus Burrata Salad', 'Pan-Seared Salmon'],
            'drink' => 'Cucumber Mint Cooler',
            'spice_pref' => 'Low',
            'dining_experience' => 'The Garden seating matches your energy.',
        ],
        'adventurer' => [
            'name' => 'The Flavor Adventurer',
            'description' => 'You’re always chasing the unexpected combination — the dish you’ve never had is more interesting than the one you have.',
            'dishes' => ['Truffle Risotto', 'Peri Peri Wings'],
            'drink' => 'Surprise Me Mocktail',
            'spice_pref' => 'Varies — you like the unexpected',
            'dining_experience' => 'Try the Surprise Me feature — it’s basically made for you.',
        ],
    ];
}
