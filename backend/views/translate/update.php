<?php

/**
 * @var string $form
 * @var \common\libs\i18n\models\Hint $model
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?=$model->isNewRecord ? 'Новый хинт' : 'Обновление хинта';?></h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12" style="width: 500px;">
        <?= $form; ?>
    </div>
</div>