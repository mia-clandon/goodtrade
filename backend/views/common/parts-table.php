<?
/**
 * Таблица с порциями для индексации.
 * @var $parts []
 * @var int $limit
 */

use yii\helpers\ArrayHelper;

?>
<table class="table">
    <thead>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </thead>
    <tbody>
    <? foreach ($parts as $part) {
        $offset = ArrayHelper::getValue($part, 'offset', 0);
        $limit_show = ArrayHelper::getValue($part, 'limit', 0);
    ?>
        <tr data-offset="<?= $offset ?>" data-limit="<?= $limit ?>">
            <td><?= $offset ?> - <?= $limit_show ?></td>
            <td>
                <button class="btn start-part btn-xs">Начать</button>
            </td>
        </tr>
    <? } ?>
    </tbody>
</table>