<?php

/* @var $content string */
use yii\helpers\Html;
use frontend\components\widgets\b2b\UserActions;
use frontend\components\widgets\b2b\Footer;
use frontend\components\widgets\b2b\SideBar;
use frontend\components\widgets\b2b\NavBar;
use frontend\components\widgets\b2b\Register;
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?= Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?></title>
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <!-- <![endif] -->
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>

<?= SideBar::widget(); // левое меню. ?>
<?= NavBar::widget(); // верхнее навигационное меню. ?>

<div class="wrapper">
    <?= ($content) ? $content : ''; ?>

    <? // TODO сделать верстку на новую UserActions::widget(); // блок с модальным окном для авторизации. ?>
    <?= Footer::widget(); // футер. ?>
</div>

<?= UserActions::widget(); // блок с модальным окном для авторизации. ?>
<?= Register::widget(); // блок с регистрацией пользователя. ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KWJQVB"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>