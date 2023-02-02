<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Произошла ошибка во время выполнения вашего запроса.
    </p>
    <p>
        Если тут не должно было быть ошибки напиши мне на почту - <a href="mailto:kowapssupport@gmail.com">kowapssupport@gmail.com</a>.
    </p>
</div>