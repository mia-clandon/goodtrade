<?php
/**
 * @var boolean $is_guest
 * @var string $breadcrumbs
 * @var \common\models\User|null $user
 * @var boolean|string $avatar
 * @author Артём Широких kowapssupport@gmail.com
 */

use frontend\components\widgets\Favorite;
use frontend\components\widgets\Compare;
use frontend\components\widgets\NotifyManager;

?>

<header class="navbar-wrap">
    <nav id="navbar" class="navbar-white">
        <div class="navbar-left-side">
            <button class="menu"></button>
            <div class="logo logo-sm"><a href="<?=Yii::$app->urlManager->createUrl(['site/index']);?>"></a></div>
        </div>
        <?= $breadcrumbs; ?>
        <?
        // меню для гостя.
        if ($is_guest) { ?>
            <div class="pull-right">
                <div class="snippets">
                    <ul>
                        <? /** Избранное. */ ?>
                        <?= Favorite::widget(); ?>
                        <? /** Выбранные товары для сравнения. */ ?>
                        <?= Compare::widget(); ?>
                    </ul>
                </div>
                <div class="navbar-link-wrap">
                    <a role="button" href="#" class="navbar-link navbar-link-muted modal-btn">Войти</a>
                    <a role="button" href="#footer-form" class="navbar-link">Получить бесплатный доступ</a>
                </div>
            </div>
        <? }
        else {
        ?>
        <div class="pull-right">
            <div class="snippets">
                <ul>
                    <? /** Избранное. */ ?>
                    <?= Favorite::widget(); ?>
                    <? /** Выбранные товары для сравнения. */ ?>
                    <?= Compare::widget(); ?>
                    <? /** Центр уведомлений. */ ?>
                    <?= NotifyManager::widget(); ?>
                    <? /*
                    <li class="snippets-item">
                        <div class="action"><span class="action-icon action-icon-favorite"><span
                                    class="action-count">1</span></span></div>
                    </li>
                    */ ?>
                </ul>
            </div>
            <div class="user-profile dropdown">
                <div class="user-profile-avatar" <?if ($avatar){?>style="background-image: url(<?= $avatar; ?>);" <?}?>></div>
                <a role="button" href="#" class="user-profile-title dropdown-toggle"><?= $user->username; ?></a>
                <div class="dropdown-menu dropdown-menu-profile">
                    <div class="dropdown-menu-head">
                        <div class="dropdown-menu-title"><?= $user->phone_real; ?></div>
                    </div>
                    <div class="dropdown-menu-body">
                        <ul class="profile-menu">
                            <li class="profile-menu__item"><a href="<?=Yii::$app->urlManager->createUrl(['cabinet/profile']);?>">Профиль</a></li>
                            <li class="profile-menu__item"><a href="<?=Yii::$app->urlManager->createUrl(['cabinet/company']);?>">Компания</a></li>
                            <li class="profile-menu__item profile-menu__item_has-button">
                                <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product']);?>">Товары</a>
                                <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product/add']);?>" class="btn btn-sm btn-lowercase">Добавить</a>
                            </li>
                            <!--<li class="profile-menu__item profile-menu__item_secondary"><a class="link link-muted">Настройки</a></li>-->
                            <li class="profile-menu__item profile-menu__item_secondary"><a>Помощь</a></li>
                            <li class="profile-menu__item profile-menu__item_secondary"><a href="<?= Yii::$app->urlManager->createUrl(['logout']);?>">Выход</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <? } ?>
    </nav>
</header>