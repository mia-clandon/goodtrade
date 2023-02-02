<?php
/**
 * @var $model \common\models\VocabularyOption
 * @var $form string
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?=$model->isNewRecord ? 'Новое возможное значение' : "Обновление значения";?></h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <?=$form;?>
    </div>
</div>