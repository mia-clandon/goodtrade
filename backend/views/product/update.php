<?php

/**
 * @var \common\models\goods\Product $model
 * @var string $form
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?= $model->isNewRecord ? 'Новый товар пользователя' : "Обновление карточки товара"; ?></h1>
    </div>
</div>

<div class="card shadow p-4">
    <div class="row">
        <div class="col-lg-12">
            <?= $form; ?>
        </div>
    </div>
</div>