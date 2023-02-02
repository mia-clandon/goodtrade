<?php
/**
 * @var \common\models\Category $category
 * @var Category[] $category_list
 * @var array $category_breadcrumb
 */
use common\models\Category;
?>

<div class="row">
    <div class="col-lg-12">

    <?=
    Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/category/parts/category_tabs.php'), [
        'model' => $category,
    ]);
    ?>

        <div class="relation" data-category-id="<?= $category->id ?>">
            <div class="panel panel-default category-content">
                <h4>Список связей:</h4>
                <? if (empty($category_list)) { ?>
                    <p>У категории "<?= $category->title; ?>" пока нет связей.</p>
                <? } ?>
                <? foreach ($category_list as $category) {
                    ?>
                    <div class="well related_category" data-related-category-id="<?= $category->id ?>">
                        <span class="label label-danger delete_relation">Удалить связь</span>
                        <div><i><?= $category_breadcrumb[$category->id] ?></i></div>
                        <div class="relation_title"><b><?= $category->title ?></b></div>
                    </div>
                <? } ?>
            </div>

            <input type="button" value="Подгрузить каталог" class="btn btn-primary duplicate-start">
            <input type="button" value="Дублировать" class="btn btn-primary duplicate-button">
            <input type="button" value="Дублировать здесь" class="btn duplicate-here-button">
            <input type="button" value="Cоздать категорию здесь" class="btn create-here-button">
            <input type="button" value="Создать копию внутри" class="btn btn-default duplicate-in-button">
            <input type="button" value="Создать категорию внутри" class="btn btn-default create-new-category">
            <input type="button" value="Закрыть" class="btn btn-link close-button"/>

        </div>
    </div>
</div>