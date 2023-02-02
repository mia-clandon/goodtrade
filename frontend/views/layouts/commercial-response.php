<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\components\widgets\NoScript;

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
        <!-- <![endif] -->
        <?php $this->head(); ?>
    </head>
    <body class="has-settings has-top-controls">
    <?php $this->beginBody(); ?>

    <?= ($content) ? $content : ''; ?>

    <?php $this->endBody(); ?>
    <?= NoScript::widget(); ?>

    </body>
</html>
<?php $this->endPage(); ?>