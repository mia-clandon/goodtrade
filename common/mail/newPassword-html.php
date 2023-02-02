<?
/**
 * @var $user common\models\User
 * @var string $password
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;

?>
<div class="new-password">
    <p><?= Html::encode($user->username) ?>, добро пожаловать на GoodTrade.kz !</p>
    <p>Ваш пароль для доступа на площадку: <?= $password; ?></p>
</div>