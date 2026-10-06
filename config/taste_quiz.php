<?php
/**
 * Taste Personality quiz — questions, options, and scoring weights.
 * Each option contributes weighted points toward one or more
 * personality keys (see config/taste_personalities.php). Rendered
 * by pages/discover.php and scored by api/taste.php, so the two
 * never drift out of sync.
 */

declare(strict_types=1);

function zaiqa_taste_questions(): array
{
    return [
        [
            'id' => 'spice',
            'text' => 'How do you like your spice?',
            'options' => [
                ['id' => 'mild', 'label' => 'Mild — keep it gentle', 'weights' => ['comfort' => 2, 'classic' => 1]],
                ['id' => 'medium', 'label' => 'Medium — a little kick', 'weights' => ['classic' => 2, 'comfort' => 1]],
                ['id' => 'hot', 'label' => 'Hot — bring the heat', 'weights' => ['fire' => 2, 'adventurer' => 1]],
                ['id' => 'fire', 'label' => 'As hot as it gets', 'weights' => ['fire' => 3]],
            ],
        ],
        [
            'id' => 'sweet_savory',
            'text' => 'Sweet or savory — where do you land?',
            'options' => [
                ['id' => 'sweet', 'label' => 'Sweet tooth, always', 'weights' => ['sweet' => 3]],
                ['id' => 'savory', 'label' => 'Savory over sweet', 'weights' => ['classic' => 1, 'comfort' => 1]],
                ['id' => 'balance', 'label' => 'A balance of both', 'weights' => ['fresh' => 1, 'adventurer' => 1]],
            ],
        ],
        [
            'id' => 'food_type',
            'text' => 'What sounds best right now?',
            'options' => [
                ['id' => 'classics', 'label' => 'Comfort classics', 'weights' => ['comfort' => 3]],
                ['id' => 'bold', 'label' => 'Bold & spicy', 'weights' => ['fire' => 3]],
                ['id' => 'elegant', 'label' => 'Elegant & fine dining', 'weights' => ['classic' => 2, 'adventurer' => 1]],
                ['id' => 'fresh_light', 'label' => 'Fresh & light', 'weights' => ['fresh' => 3]],
                ['id' => 'surprise', 'label' => 'Surprise me with anything', 'weights' => ['adventurer' => 3]],
            ],
        ],
        [
            'id' => 'rich_light',
            'text' => 'Rich and indulgent, or light and fresh?',
            'options' => [
                ['id' => 'rich', 'label' => 'Rich & indulgent', 'weights' => ['comfort' => 2, 'sweet' => 1]],
                ['id' => 'light', 'label' => 'Light & fresh', 'weights' => ['fresh' => 3]],
                ['id' => 'depends', 'label' => 'Depends entirely on my mood', 'weights' => ['adventurer' => 2]],
            ],
        ],
        [
            'id' => 'adventure',
            'text' => 'How adventurous are you with new cuisines?',
            'options' => [
                ['id' => 'rarely', 'label' => 'Rarely — I know what I like', 'weights' => ['classic' => 3]],
                ['id' => 'sometimes', 'label' => 'Sometimes, if it’s recommended', 'weights' => ['comfort' => 1, 'fresh' => 1]],
                ['id' => 'always', 'label' => 'Always exploring something new', 'weights' => ['adventurer' => 3, 'fire' => 1]],
            ],
        ],
        [
            'id' => 'mood',
            'text' => 'What’s tonight’s dining mood?',
            'options' => [
                ['id' => 'cozy', 'label' => 'Cozy & familiar', 'weights' => ['comfort' => 3]],
                ['id' => 'exciting', 'label' => 'Exciting & bold', 'weights' => ['fire' => 3]],
                ['id' => 'romantic', 'label' => 'Romantic & refined', 'weights' => ['classic' => 2, 'sweet' => 1]],
                ['id' => 'energizing', 'label' => 'Light & energizing', 'weights' => ['fresh' => 3]],
            ],
        ],
    ];
}
