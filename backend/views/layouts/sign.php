<?php

/**
 * Чистый Layout для Входа в систему.
 * @var $this View
 * @var $content string
 */

use backend\assets\SignAsset;
use yii\helpers\Html;
use yii\web\View;

SignAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>">
<head>
    <meta charset="<?= Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body class="bg-primary">
<?php $this->beginBody(); ?>
    <?=$content;?>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>