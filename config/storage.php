<?php
return [
    'base_directory' => 'storage',
    'directories' => [
        'category' => [
            'directory' => 'category',
            'length' => 4,
        ],
        'product' => [
            // название папки в которой будут храниться файлы.
            'directory' => 'product',
            // количество вложенных друг в друга директорий, к примеру при 4х cd/as/r1/e3
            'length' => 4,
        ],
        'company' => [
            'directory' => 'company',
            'length' => 4,
        ],
        'product-price' => [
            'directory' => 'product-price',
            'length' => 4,
        ],
        'main_slider' => [
            'directory' => 'main_slider',
            'length' => 4,
        ],
        'category_slider' => [
            'directory' => 'category_slider',
            'length' => 4,
        ]
    ],
];