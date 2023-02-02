<?
/**
 * Таблица со списком ОКЭД.
 * @var \common\models\Oked[] $oked_list
 * @author Артём Широких kowapssupport@gmail.com
 */
?>

<form class="form-check-inline">
    <div class="form-group mr-4">
        <label>Код</label>
        <input type="text" class="key-input form-control" placeholder="Код">
    </div>
    <div class="form-group">
        <label>Название</label>
        <input type="text" class="name-input form-control" placeholder="Название">
    </div>
</form>

<table class="table">
    <thead>
    <tr>
        <td>#</td>
        <td>Код</td>
        <td>Название</td>
    </tr>
    </thead>
    <tbody>
    <? foreach ($oked_list as $oked_item) { ?>
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
    <? } ?>
    </tbody>
</table>