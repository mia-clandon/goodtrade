<?php
/**
 * @var boolean $is_landing
 * @var boolean $is_guest
 * @var string $breadcrumbs
 * @var \common\models\User|null $user
 * @var boolean|string $avatar
 * @var string $controller
 * @var string $search_form
 * @author yerganat
 */

use frontend\components\widgets\b2b\Favorite;
use frontend\components\widgets\b2b\Compare;
use frontend\components\widgets\b2b\NotifyManager;
use frontend\components\widgets\b2b\Deal;

?>

<header<?= $is_landing ? ' class="header-absolute"' : ''; ?>>
    <div class="bar-top">
        <div class="bar-top__left-part">
            <div class="bar-top__menu-button-container">
                <button class="bar-top__menu-button"></button>
            </div>
            <div class="bar-top__logo-container">
                <a href="/" class="logo <?= $is_landing ? 'logo_sm' : 'logo_sm-short'; ?>" title="B2B"></a>
            </div>
            <?= $breadcrumbs; ?>
        </div>

        <div class="bar-top__search">
            <?= $search_form; // в зависимости от контроллера - подгружается нужная форма. ?>
            <div class="bar-top__search-toggle">
                <div class="icon icon_search"></div>
            </div>
        </div>
        <div class="bar-top__right-part">
            <div class="bar-top__user-features">

                <? /** Избранное. */ ?>
                <?= Favorite::widget(); ?>

                <? /** Выбранные товары для сравнения. */ ?>
                <?= Compare::widget(); ?>

                <? /** Сделки. */ ?>
                <?= Deal::widget(); ?>

            </div>
            <?
            // меню для гостя.
            if ($is_guest) { ?>

                <div class="bar-top__buttons-block">
                    <button class="button button_link" data-type="modal-open" data-modal="overlay-sign-up">
                        <span class="button__text">Войти</span>
                    </button>
                    <button class="button"  data-type="modal-open" data-modal="overlay-register">
                        <span class="button__text">Регистрация</span>
                    </button>
                </div>

            <? } else { ?>

                <div class="bar-top__user-block">

                    <? /** Центр уведомлений. */ ?>
                    <?= NotifyManager::widget(); ?>

                    <div class="dropdown b2b">
                        <div class="bar-top__user-avatar dropdown__toggle">
                            <img src="<?=$avatar?$avatar:'/img/no-avatar.png' ?>" alt="Логотип компании">
                        </div>
                        <div class="dropdown__item dropdown__item_user-avatar">
                            <div class="dropdown__item-indicator"></div>
                            <div class="modal">
                                <div class="modal__title"><?= $user->username; ?></div>
                                <ul class="profile-menu">
                                    <li class="profile-menu__item"><a href="<?=Yii::$app->urlManager->createUrl(['cabinet/profile']);?>">Профиль</a></li>
                                    <li class="profile-menu__item"><a href="<?=Yii::$app->urlManager->createUrl(['cabinet/company']);?>">Компания</a></li>
                                    <li class="profile-menu__item">
                                        <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product']);?>">Товары</a>
                                        <button class="button button_small">
                                            <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product/add']);?>" class="button__text">Добавить</a>
                                        </button>
                                    </li>
                                    <li class="profile-menu__item profile-menu__item_small"><a href="#">Помощь</a></li>
                                    <li class="profile-menu__item profile-menu__item_small"><a href="<?= Yii::$app->urlManager->createUrl(['logout']);?>">Выход</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>
        </div>
    </div>
    <div class="bar-bottom">
        <? /** Избранное. */ ?>
        <?= Favorite::widget(); ?>
        <? /** Выбранные товары для сравнения. */ ?>
        <?= Compare::widget(); ?>
        <? /** Сделки. */ ?>
        <?= Deal::widget(); ?>
        <? if (!$is_guest) { ?>
            <? /** Центр уведомлений. */ ?>
            <?= NotifyManager::widget(); ?>
        <? } else { ?>
            <div class="bar-bottom__part"></div>
        <? } ?>
        <? if ($is_guest) { ?>
            <div class="bar-bottom__part">
                <div class="bar-bottom__user-avatar" data-type="modal-open" data-modal="overlay-sign-up">
                    <img src="<?=$avatar?$avatar:'/img/no-avatar.png' ?>" alt="Логотип компании">
                </div>
            </div>
        <? } else { ?>
            <div class="dropdown b2b">
                <div class="bar-bottom__user-avatar dropdown__toggle">
                    <img src="<?=$avatar?$avatar:'/img/no-avatar.png' ?>" alt="Логотип компании">
                </div>
                <div class="dropdown__item dropdown__item_user-avatar">
                    <div class="dropdown__item-indicator"></div>
                    <div class="modal">
                        <div class="modal__title"><?= $user->username; ?></div>
                        <ul class="profile-menu">
                            <li class="profile-menu__item"><a href="<?=Yii::$app->urlManager->createUrl(['cabinet/profile']);?>">Профиль</a></li>
                            <li class="profile-menu__item"><a href="<?=Yii::$app->urlManager->createUrl(['cabinet/company']);?>">Компания</a></li>
                            <li class="profile-menu__item">
                                <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product']);?>">Товары</a>
                                <button class="button button_small">
                                    <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product/add']);?>" class="button__text">Добавить</a>
                                </button>
                            </li>
                            <li class="profile-menu__item profile-menu__item_small"><a href="#">Помощь</a></li>
                            <li class="profile-menu__item profile-menu__item_small"><a href="<?= Yii::$app->urlManager->createUrl(['logout']);?>">Выход</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</header>
