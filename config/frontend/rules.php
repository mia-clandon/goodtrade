<?php

return [
    'join' => 'site/join',
    'change-password/<token:\w+>' => 'site/change-password',
    'logout' => 'site/logout',
    'product/<id:\d+>' => 'product/show',
    'cabinet' => 'cabinet/profile',
    'firm/<id:\d+>' => 'firm/show',
    'page/<page:\w+>.html' => 'page/show',
    'commercial/response/<request:\d+>' => 'commercial/response',
    'commercial/response/show/<response:\d+>' => 'commercial/show-response',
    'category/<id:\d+>' => 'category/show',
    'favorite/<firm_id:\d+>' => 'favorite/index',
    'cabinet/product/update/<id:\d+>' => 'cabinet/product/update',
];