<?
/**
 * @var \common\models\Category $category;
 * @var string $form
 */
?>

<div class="row">
    <div class="col-lg-12">
        <?=
        Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/category/parts/category_tabs.php'), [
            'model' => $category,
        ]);
        ?>
        <h1 class="h2 mb-2 text-gray-800">Окэды категории "<?= $category->title; ?>".</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-10">
        <?= $form; ?>
    </div>
</div>