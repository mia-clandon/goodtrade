<?
/**
 * @var array $oked_list
 * @var \common\models\Oked $oked
 */

foreach ($oked_list as $oked_item) {
?>

    <tr>
        <td>
            <label>
                <input value="<?= $oked_item->key; ?>" type="checkbox" name="oked" />
            </label>
        </td>
        <td>
            <span class="label label-default"><?= $oked_item->key; ?></span>
        </td>
        <td>
            <span><?= $oked_item->name; ?></span>
        </td>
    </tr>

<? }