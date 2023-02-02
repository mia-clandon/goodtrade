<?
/**
 * @var \common\models\Page $model
 * @var string $form
 */
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?=$model->isNewRecord ? 'Новая страница сайта' : "Обновление страницы";?></h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?=$form;?>
    </div>
</div>