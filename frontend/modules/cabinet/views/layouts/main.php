<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\helpers\Html;
use frontend\components\widgets\NoScript;
use frontend\components\widgets\NavBar;
use frontend\components\widgets\cabinet\InnerNavBar;
use frontend\components\helper\Alert;

$this->beginPage();
?>
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
    <?php Alert::register();?>
</head>
<body class="has-sidebar has-bottom-control">
<?php $this->beginBody(); ?>

    <?=NavBar::widget(); // верхнее навигационное меню. ?>

<main role="main" id="page-wrap">

    <?=InnerNavBar::widget(); // левое меню. ?>
    <?= ($content) ? $content : ''; ?>

</main>
<?= NoScript::widget(); ?>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>