<?php

#region Правило для Resize изображений.

use common\libs\StringBuilder;

// собираю правило для передачи фото контроллеру.
$image_rule = new StringBuilder();
// название директории для хранения файлов
$image_rule->add('/storage/');
// название хранилища
$image_rule->add('<storage:([^\/]+)>');
// путь до файла
$image_rule->add('<dir:(.*[\\\/]+)>');
// название файла
$image_rule->add('<file:([a-z0-9]+)>');
// информация о размерах
$image_rule->add('<size:([_]\d+([_]\d+)?)?>');
// информация о типе ресайза
$image_rule->add('<mode:(auto|height|width|none|crop)?>');
// расширение
$image_rule->add('.<ext:(gif|jpg|jpeg|tiff|png)>');

$image_rule = $image_rule->get();

#endregion

return [
    $image_rule => 'image/default/index',
];