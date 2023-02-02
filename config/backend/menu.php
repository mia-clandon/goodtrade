<?php

use common\libs\helpers\Permission;

return [
    [
        'name' => 'Главная страница',
        'icon' => 'fa fa-dashboard fa-fw',
        'permissions' => [Permission::ROLE_ADMIN],
        'sub' => [
            [
                'name' => 'Панель управления',
                'url' => '/',
            ],
            [
                'name' => 'Слайдер',
                'url' => 'slider',
            ],
        ],
    ],
    [
        'name' => 'Пользователи',
        'icon' => 'fa fa-user',
        'url' => 'user',
        'permissions' => [Permission::ROLE_ADMIN],
    ],
    [
        'name' => 'Управление каталогом',
        'icon' => 'fa fa-folder fa-fw',
        'permissions' => [Permission::ROLE_ADMIN],
        'sub' => [
            [
                'name' => 'Категории каталога',
                'url' => 'category',
            ],
            [
                'name' => 'Характеристики',
                'url' => 'vocabulary',
            ],
            [
                'name' => 'ОКЭД каталог',
                'url' => 'oked',
            ],
            [
                'name' => 'Связи ОКЭД\'ов',
                'url' => 'oked/relation',
            ],
            [
                'name' => 'Слайдер',
                'url' => 'category-slider',
            ],
        ],
    ],
    [
        'name' => 'Организации',
        'icon' => 'fa fa-briefcase fa-fw',
        'permissions' => [Permission::ROLE_ADMIN],
        'sub' => [
            [
                'name' => 'Организации площадки',
                'icon' => 'fa fa-list fa-fw',
                'url' => 'firm',
            ],
            [
                'name' => 'Список из парсера',
                'icon' => 'fa fa-list fa-fw',
                'url' => 'profile',
            ],
        ]
    ],
    [
        'name' => 'Товары',
        'icon' => 'fa fa-folder-o fa-fw',
        'permissions' => [Permission::ROLE_ADMIN],
        'sub' => [
            [
                'name' => 'Товары пользователей',
                'url' => 'product',
            ],
            [
                'name' => 'Выгрузка прайс листа',
                'url' => 'import',
            ],
        ],
    ],
    [
        'name' => 'Города',
        'icon' => 'fa fa-home fa-fw',
        'permissions' => [Permission::ROLE_ADMIN],
        'sub' => [
            [
                'name' => 'Список городов',
                'icon' => 'fa fa-list fa-fw',
                'url' => 'location/index',
            ],
            [
                'name' => 'Добавить город',
                'icon' => 'fa fa-plus fa-fw',
                'url' => 'location/update',
            ]
        ],
    ],
    [
        'name' => 'Страницы сайта',
        'icon' => 'fa fa-file fa-fw',
        'url' => 'page/index',
        'permissions' => [Permission::ROLE_ADMIN],
    ],
    [
        'name' => 'Переводы',
        'icon' => 'fa fa-behance fa-fw',
        'url' => 'translate/index',
        'permissions' => [Permission::ROLE_TRANSLATOR],
    ],
    [
        'name' => 'Очистить кеш',
        'icon' => 'fa fa-braille fa-fw',
        'url' => 'cache/clear',
        'permissions' => [Permission::ROLE_ADMIN],
    ]
];