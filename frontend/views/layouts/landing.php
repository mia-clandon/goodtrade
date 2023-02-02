<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

use frontend\components\widgets\b2b\UserActions;
use frontend\components\widgets\b2b\Footer;
use frontend\components\widgets\b2b\SideBar;

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?= Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>

    <?=SideBar::widget(); // левое меню. ?>

    <header class="header-absolute">
        <div class="bar-top bar-top_inverted">
            <div class="bar-top__left-part">
                <div class="bar-top__menu-button-container">
                    <button class="bar-top__menu-button bar-top__menu-button_white"></button>
                </div>
                <div class="bar-top__logo-container">
                    <a href="/" class="logo logo_sm-white" title="B2B"></a>
                </div>
            </div>

            <div class="bar-top__search">
                <?
                    // Форма поиска на главной странице.
                    /** @var \frontend\controllers\BaseController $controller */
                    $controller = Yii::$app->controller;
                    echo $controller->getSearchForm()->render();
                ?>
            </div>

            <div class="bar-top__right-part">
                <? if (Yii::$app->user->isGuest) { ?>
                    <div class="bar-top__buttons-block">
                        <button class="button button_link-white" data-type="modal-open" data-modal="overlay-sign-up">
                            <span class="button__text">Войти</span>
                        </button>
                        <button class="button button_white"  data-type="modal-open" data-modal="overlay-register">
                            <span class="button__text">Регистрация</span>
                        </button>
                    </div>
                <? } else { ?>
                    <div class="bar-top__user-features">
                        <div class="dropdown b2b">
                            <div class="icon icon_favorite-white dropdown__toggle"></div>
                            <div class="dropdown__item dropdown__item_bar-top-icon-center">
                                <div class="modal modal_dropdown-center">
                                    <div class="modal__title">Избранное</div>
                                    <div class="notification notification_shifted-down">
                                        <div class="notification__image notification__image_favorite"></div>
                                        <p class="notification__description">Вы пока не добавили в избранное<br>ни одну компанию или товар</p>
                                        <a href="#" class="notification__link">Зачем нужно избранное?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown b2b">
                            <div class="icon icon_comparison-white dropdown__toggle"></div>
                            <div class="dropdown__item dropdown__item_bar-top-icon-center">
                                <div class="modal modal_dropdown-center">
                                    <div class="modal__title">Сравнение</div>
                                    <div class="notification notification_shifted-down">
                                        <div class="notification__image notification__image_comparison"></div>
                                        <p class="notification__description">Вы не создали<br>ни одного сравнения</p>
                                        <a href="#" class="notification__link">Что такое сравнения?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown b2b">
                            <div class="icon icon_deal-white dropdown__toggle"></div>
                            <div class="dropdown__item dropdown__item_bar-top-icon-center">
                                <div class="modal modal_dropdown-center">
                                    <div class="modal__title">Сделки</div>
                                    <div class="notification notification_shifted-down">
                                        <div class="notification__image notification__image_deals"></div>
                                        <p class="notification__description">Вы не заключили ни одной<br>сделки на нашем сервисе</p>
                                        <a href="#" class="notification__link">Как заключить сделку?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bar-top__user-avatar">
                        <img src="/img/no-avatar.png" alt="Логотип компании">
                    </div>
                <? } ?>
            </div>
        </div>
    </header>

    <div class="wrapper">
        <?= $content; ?>
        <?= Footer::widget(); // футер. ?>
    </div>

    <?= UserActions::widget(); // блок с модальным окном для авторизации. ?>

    <?//TODO: оживить форму регистрации.?>
    <div class="overlay" data-type="modal" data-modal="overlay-register">
        <div class="container container_main">
            <div class="row">
                <div class="col-4 m-auto">
                    <div class="overlay__modal-container">
                        <div class="overlay__modal-logo-container">
                            <div class="logo logo_md-white"></div>
                        </div>
                        <div class="modal modal_overlay-login">
                            <h2 class="modal__heading">Регистрация</h2>
                            <div class="row">
                                <div class="col-4 m-auto">
                                    <form action="">
                                        <div class="form-control">
                                            <div class="form-control__top-text">
                                                <div class="form-control__label">Ваш основной телефон</div>
                                            </div>
                                            <div class="input">
                                                <input type="text" placeholder="+7 777 987 65 43">
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <div class="form-control__top-text">
                                                <div class="form-control__label">Ваш основной email</div>
                                            </div>
                                            <div class="input">
                                                <input type="text" placeholder="username@gmail.com">
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <div class="form-control__top-text">
                                                <div class="form-control__label">Пароль</div>
                                            </div>
                                            <div class="input">
                                                <input type="password" placeholder="Пароль">
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <button class="button button_full button_primary">
                                                <span class="button__text">Зарегистрироваться</span>
                                            </button>
                                        </div>
                                        <div class="form-control">
                                            <div class="checkbox">
                                                <input type="checkbox" class="checkbox__input" checked>
                                                <label class="checkbox__label checkbox__label_small"><span class="checkbox__check-mark"></span>Я даю свое согласие на обработку персональных данных и соглашаюсь с политикой конфиденциальности</label>
                                            </div>
                                        </div>
                                        <div class="form-control">
                                            <div class="checkbox">
                                                <input type="checkbox" class="checkbox__input" checked>
                                                <label class="checkbox__label checkbox__label_small"><span class="checkbox__check-mark"></span>Я хочу получать email-письма о мероприятиях и/или иных услугах</label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KWJQVB"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
