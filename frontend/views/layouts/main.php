<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\components\widgets\Register;
use frontend\components\widgets\UserActions;
use frontend\components\widgets\NavBar;
use frontend\components\widgets\SideBar;
use frontend\components\widgets\NoScript;
use frontend\components\widgets\Footer;

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title><?= Html::encode($this->title); ?></title>
        <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- <![endif] -->
        <?php $this->head(); ?>
    </head>
    <body class="<?= \Yii::$app->controller->id=='favorite'?'favorites-page has-sidebar has-bottom-controls':''?>">
    <?php $this->beginBody(); ?>

    <?=NavBar::widget(); // верхнее навигационное меню. ?>
    <?=SideBar::widget(); // левое меню. ?>

    <main role="main" id="page-wrap">

        <?= ($content) ? $content : ''; ?>

    </main>

    <?= Register::widget(); // блок с регистрацией пользователя. ?>
    <?= UserActions::widget(); // блок с модальным окном для авторизации. ?>
    <?= Footer::widget(); // футер. ?>

    <?php $this->endBody(); ?>
    <?= NoScript::widget(); ?>

    </body>
</html>
<?php $this->endPage(); ?>