<?php /** @noinspection PhpUnhandledExceptionInspection */

/* @var $this View */
/* @var $content string */

use frontend\components\widgets\NoScript;
use yii\helpers\Html;
use yii\web\View;

\frontend\assets\UploadAsset::register($this);

?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="ru" class="registration-page">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>

    <header id="top-controls">
        <nav><a href="<?= Yii::$app->urlManager->createUrl(['site/index', 'prod' => 1]); ?>"
                class="logo logo-blue pull-left"></a>
            <div id="register-progress" class="progress">
                <div class="progress-item is-success">
                    <span class="progress-item-number">1</span>
                    <span class="progress-item-label">Пользователь</span>
                </div>
                <div class="progress-arrow"></div>
                <div class="progress-item is-active">
                    <span class="progress-item-number">2</span>
                    <span class="progress-item-label">Компания</span>
                </div>
                <div class="progress-arrow"></div>
            <div class="progress-item">
                <span class="progress-item-number">3</span>
                <span class="progress-item-label">Товары</span>
            </div>
        </div>
    </nav>
</header>

<?= $content; ?>

<?php $this->endBody(); ?>

<?= NoScript::widget(); ?>

</body>
</html>
<?php $this->endPage(); ?>