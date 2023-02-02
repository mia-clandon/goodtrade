<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$reset_link = Yii::$app->urlManager->createAbsoluteUrl(['site/change-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Здравствуйте <?= Html::encode($user->username) ?>,</p>
    <p>Для восстановления пароля на площадке goodtrade.kz перейдите по следующей ссылке:</p>
    <p><?= Html::a(Html::encode($reset_link), $reset_link); ?></p>
</div>