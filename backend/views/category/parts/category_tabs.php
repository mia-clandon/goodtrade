<?
/**
 * @var \common\models\Category $model
 */
$controller_action = Yii::$app->controller->action->id;
?>
<ul class="nav nav-tabs">
    <li class="<?= $controller_action === 'update' ? 'active' : ''?>">
        <a href="<?=\yii\helpers\Url::to(['category/update', 'id' => $model->id])?>">Основное</a>
    </li>
<!--    <li><a href="#">ОКЭДы</a></li>-->
    <? if (!$model->isNewRecord) { ?>
    <li class="<?= $controller_action === 'vocabulary' ? 'active' : ''?>">
        <a href="<?=\yii\helpers\Url::to(['category/vocabulary', 'id' => $model->id])?>">Характеристики</a>
    </li>
    <li class="<?= $controller_action === 'relation' ? 'active' : ''?>">
        <a href="<?=\yii\helpers\Url::to(['category/relation', 'id' => $model->id])?>">Связи</a>
    </li>
    <li class="<?= $controller_action === 'oked' ? 'active' : ''?>">
        <a href="<?=\yii\helpers\Url::to(['category/oked', 'id' => $model->id])?>">Окэды</a>
    </li>
    <? } ?>
<!--    <li><a href="#">СЕО</a></li>-->
</ul>