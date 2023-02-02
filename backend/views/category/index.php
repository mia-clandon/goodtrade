<?php
/**
 * @var Category[] $activities - сферы деятельности.
 * @var null|integer $current_activity_id
 * @var Category[] $category_list
 * @var Category $current_activity
 * @var boolean $catalog_is_ajax_version
 * @var string $catalog_html
 * @var string $last_updated_date
 */

use common\models\Category;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?= ($current_activity_id === null) ? 'Категории каталога' : $current_activity->title; ?></h1>
    </div>
</div>

<?
// обновление каталога.
if ($current_activity_id !== null && !$catalog_is_ajax_version) {
    echo \yii\bootstrap\Html::a('Обновить каталог', '#', ['data-parent-id'=>$current_activity_id, 'class'=>'btn btn-warning update-catalog-button']);
}
?>

<div class="card shadow mb-4 pt-3 pl-3 pr-3 pb-0">
    <? if ($current_activity_id === null) { ?>
        <p>Последнее обновление каталога: <?= $last_updated_date; ?></p>
    <? } ?>
</div>
<div class="card shadow mb-4">
    <div class="card-body p-0">
<?
// Блок выбора сферы деятельности.
if ($current_activity_id === null) { ?>
        <div class="card-header pt-3 pb-2 ">
            <h6 class="font-weight-bold text-primary">
                Выберите сферу деятельности
            </h6>
        </div>
            <div class="row p-3">
                <? foreach ($activities as $activity) {
                    $active_class=$activity->id === $current_activity_id ? 'thumbnail_active' : '';
                    ?>
                    <div class="d-flex flex-row col-xs-4 col-sm-6 activity-block">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/category/index', 'activity_id'=>$activity->id]) ?>"
                           class="p-2 thumbnail <?= $activity->icon_class; ?> <?= $active_class; ?>">
                            <?= $activity->title; ?>
                        </a>
                        <div class="p-2 actions">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/category/update', 'id'=>$activity->id]); ?>">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                            <a class="remove" data-confirm="Вы действительно хотите удалить категорию ?"
                               href="<?= Yii::$app->urlManager->createUrl(['/category/delete', 'id'=>$activity->id]); ?>">
                                <span class="glyphicon glyphicon-remove text-danger"></span>
                            </a>
                        </div>
                    </div>
                <? } ?>
        </div>
<? } ?>
</div>
</div>
<div class="modal fade" id="modal-oked" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Привязка ОКЭД к категории</h4>
            </div>
            <div class="modal-body">
                <div class="content" id="modal-oked-content"><? // сюда подгрузится список с ОКЕД.?></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal-add-category-vocabulary" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Добавление характеристики к категории</h4>
            </div>
            <div class="modal-body">
                <div class="content"
                     id="modal-add-category-vocabulary-content"><? // сюда подгрузится форма для привязки характеристики.?></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal-oked-category-relation" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Добавление ОКЭД к категории</h4>
            </div>
            <div class="modal-body">
                <div class="content" id="modal-oked-category-relation-content">
                    <? // сюда подгрузится форма для привязки окэда к категории.?>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="catalog-wrapper">
    <?
    if ($current_activity_id !== null) {
        if ($catalog_is_ajax_version) {
            echo Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/category/parts/ajax_categories.php'), ['category_list'=>$category_list,]);
        } else {
            echo $catalog_html;
        }
    }
    ?>
</div>
<div class="form-inline">
    <?
    $options=['category/update'];
    if ($current_activity_id !== null) {
        $options['parent_id']=$current_activity_id;
    }
    echo \yii\bootstrap\Html::a('Добавить категорию', $options, ['class'=>'btn btn-primary']); ?>
    &nbsp;
    <? echo \yii\bootstrap\Html::a('Выгрузить в Excel', ['category/export'], ['class'=>'btn btn-success']); ?>
</div>