<?php
/**
 * @var $location \common\models\Location
 * @var $location_form string
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?= $location->isNewRecord ? 'Добавить город' : "Обновить город"; ?></h1>
    </div>
</div>

<div class="card shadow mb-4 p-4">
    <div class="row">
        <div class="col-lg-12">
            <?= $location_form; ?>
        </div>
    </div>
</div>