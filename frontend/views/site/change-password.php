<?
/**
 * @var \common\models\User $user
 * @var boolean $result
 */
?>
<div class="row">
    <div class="col-xs-6">
        <? if ($result) { ?>
        <p class="sub-title text-center text-lg-row-2x">Здравствуйте, вам был выслан новый пароль на почту "<?= $user->email; ?>".</p>
        <? } else { ?>
        <p class="sub-title text-center text-lg-row-2x">Произошла ошибка восстановления пароля, попробуйте позже.</p>
        <? } ?>
    </div>
</div>