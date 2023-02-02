<?
/**
 * @var string $controller_name
 * @var string $action_name
 */
?>
<div class="inner-sidebar">
    <ul class="inner-sidebar-menu">
        <li class="<?=$controller_name=='profile' && $action_name=='index'?'is-active':'';?>">
            <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/profile']);?>">Профиль</a>
        </li>
        <li class="<?=$controller_name=='company' && $action_name=='index'?'is-active':'';?>">
            <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/company']);?>">Компания</a>
        </li>
        <li class="has-button <?=$controller_name=='product' && in_array($action_name, ['index', 'add'])?'is-active':'';?>">
            <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product']);?>">Товары</a>
            <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product/add']);?>" class="btn btn-sm btn-lowercase btn-outline">Добавить</a>
        </li>
    </ul>
<!--    <ul class="inner-sidebar-settings">-->
<!--        <li><a href="#" class="muted">Настройки</a></li>-->
<!--        <li><a href="#" class="muted">Помощь</a></li>-->
<!--    </ul>-->
</div>