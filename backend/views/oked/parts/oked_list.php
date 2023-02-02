<?
/**
 * @var array $oked_list
 * @var bool $with_checkbox
 * @var bool $with_delete
 * @var bool $with_reveal
 * @var \common\models\Oked $oked
 */

foreach ($oked_list as $oked_item) {
?>

    <div class="item">
        <?=$with_checkbox?'<input type="checkbox"/>':''?>
        <span class="code"><?= $oked_item->key; ?></span>
        <?=$with_delete ?'<span class="delete">Удалить</span>':''?>
        <?=$with_reveal ?'<span class="reveal">Показать связи</span>':''?>
        <div class="title"><?= $oked_item->name; ?></div>
    </div>

<? }