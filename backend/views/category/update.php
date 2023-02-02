<?
/**
 * @var $model \common\models\Category
 * @var string $form
 */
?>
<div class="row">
    <div class="col-lg-12">
        <?=
        Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/category/parts/category_tabs.php'), [
            'model' => $model,
        ]);
        ?>
        <h1 class="page-header"><?=$model->isNewRecord ? 'Новая категория каталога' : 'Редактирование категории';?></h1> </div>
</div>

<div class="card shadow mb-4 p-4">
    <?= $form; // форма редактирования/добавления категории.  ?>
</div>
