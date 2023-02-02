<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

//use yii\helpers\Html;

/** @var \frontend\controllers\SiteController $controller */
$controller = Yii::$app->controller;
$controller->seo->title = $name;
?>

<? /*?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert alert-danger"><?= nl2br(Html::encode($message)) ?></div>
    <p>Произошла ошибка во время выполнения вашего запроса.</p>
    <p>Please contact us if you think this is a server error. Thank you.</p>
</div>
*/ ?>

<div class="row">
    <div class="col-xs-6">
        <div class="error">
            <div class="error-text">Упс...</div>
            <div class="error-code">404</div>
            <ul class="error-links">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['site/index']); ?>">Вернуться назад</a></li>
                <li><a href="<?= Yii::$app->urlManager->createUrl(['site/index']); ?>">Вернуться на главную</a></li>
            </ul>
        </div>
    </div>
</div>