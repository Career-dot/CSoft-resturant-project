<?php
/**
 * Build-your-own customization options, keyed by dish id. Only
 * dishes present here show a "Customize" step before adding to
 * the order; everything else adds as-is. Extend this array to
 * make another dish customizable — the frontend (menu.js) reads
 * whatever groups are defined here.
 */

declare(strict_types=1);

function zaiqa_customization_options(): array
{
    return [
        10 => [ // Zaiqa Smash Burger
            'groups' => [
                [
                    'id' => 'bun', 'label' => 'Bread', 'type' => 'single',
                    'options' => [
                        ['id' => 'brioche', 'label' => 'Brioche', 'price_delta' => 0],
                        ['id' => 'sesame', 'label' => 'Sesame', 'price_delta' => 0],
                        ['id' => 'multigrain', 'label' => 'Multigrain', 'price_delta' => 50],
                    ],
                ],
                [
                    'id' => 'sauce', 'label' => 'Sauce', 'type' => 'single',
                    'options' => [
                        ['id' => 'garlic', 'label' => 'Garlic', 'price_delta' => 0],
                        ['id' => 'bbq', 'label' => 'BBQ', 'price_delta' => 0],
                        ['id' => 'spicy', 'label' => 'Spicy', 'price_delta' => 0],
                    ],
                ],
                [
                    'id' => 'cheese', 'label' => 'Cheese', 'type' => 'single',
                    'options' => [
                        ['id' => 'cheddar', 'label' => 'Cheddar', 'price_delta' => 0],
                        ['id' => 'mozzarella', 'label' => 'Mozzarella', 'price_delta' => 50],
                    ],
                ],
                [
                    'id' => 'extras', 'label' => 'Extras', 'type' => 'multi',
                    'options' => [
                        ['id' => 'extra_cheese', 'label' => 'Extra Cheese', 'price_delta' => 100],
                        ['id' => 'jalapenos', 'label' => 'Jalapeños', 'price_delta' => 70],
                        ['id' => 'mushrooms', 'label' => 'Mushrooms', 'price_delta' => 90],
                    ],
                ],
            ],
        ],
    ];
}
