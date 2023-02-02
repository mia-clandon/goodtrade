<?php

/**
 * @var \common\models\MainSlider $model
 */
?>

<div class="row">
    <div class="col-lg-12">
        <? if (empty($model->slide_id)) { ?>
            <h1 class="h2 mb-2 text-gray-800"><?= $model->isNewRecord ? 'Новый слайд ' : "Обновление слайда: " . $model->title; ?></h1>
        <? } else {
            $slide=\common\models\MainSlider::findOne(['id'=>$model->slide_id])
            ?>
            <h1 class="h2 mb-2 text-gray-800"><?= $model->isNewRecord ? 'Новый элемент слайда: ' : "Обновление элемента слайда: "; ?> <?= $slide->title ?></h1>
        <? } ?>
    </div>
</div>


<div class="card shadow mb-4 p-4">
    <div class="row slider-form">
        <div class="col-lg-12">
            <?= $form; ?>
        </div>
    </div>
</div>