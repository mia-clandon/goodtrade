<?

use common\models\Vocabulary;
use yii\bootstrap\Html;

/**
 * @var \common\models\Category $category;
 * @var array $vocabularies
 */
$vocabulary_model = new Vocabulary();

?>

<style>
    .arrows {
        width: 10%;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <?=
        Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/category/parts/category_tabs.php'), [
            'model' => $category,
        ]);
        ?>
        <h1 class="h2 mb-2 text-gray-800">Характеристики категории "<?= $category->title; ?>".</h1>
    </div>
</div>

<div class="modal fade" id="modal-add-category-vocabulary" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Добавление характеристики к категории</h4>
            </div>
            <div class="modal-body">
                <div class="content" id="modal-add-category-vocabulary-content"><?// сюда подгрузится форма для привязки характеристики.?></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row">
    <div class="col-lg-10">
        <table class="table table-bordered dataTable">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Заголовок</td>
                    <td>Тип</td>
                    <td>Ед.измер.</td>
                    <td>Характеристика участвует в названии</td>
                    <td>Использовать только название характеристики в названии товара</td>
                    <td>Использовать только значение характеристики в названии товара</td>
                    <td></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <? foreach ($vocabularies as $vocabulary) {

                    # основные параметры.
                    $vocabulary_id = (int)$vocabulary['id'];
                    $vocabulary_category_id = (int)$vocabulary['category_id'];
                    $vocabulary_title = $vocabulary['title'] ?? '';
                    $type_name = $vocabulary_model->getTypeName($vocabulary['type'] ?? 0);
                    $type = (int)$vocabulary['type'];

                    $use_in_product_name = (bool)$vocabulary['use_in_product_name'] ?? false;
                    $use_only_vocabulary_name = (bool)$vocabulary['use_only_vocabulary_name'] ?? false;
                    $use_only_vocabulary_value = (bool)$vocabulary['use_only_vocabulary_value'] ?? false;

                    # данные по диапазону.
                    $range_from = (float)$vocabulary['range_from'] ?? 0;
                    $range_to = (float)$vocabulary['range_to'] ?? 0;
                    $range_step = (float)$vocabulary['range_step'] ?? 0;

                    // единица измерения.
                    $unit = \common\models\Unit::findByCodeCached($vocabulary['unit_code'] ?? 0);
                    $full_unit_string = null !== $unit ? $unit->title.' '.$unit->symbol_national : ' &mdash; ';
                    $short_unit_string = null !== $unit ? $unit->symbol_national : '';
                ?>
                <tr data-current-category-id="<?= $category->id; ?>" data-category-id="<?= $vocabulary_category_id; ?>" data-vocabulary-id="<?= $vocabulary_id; ?>">
                    <td><?=$vocabulary['id'];?></td>
                    <td style="width: 25%;">
                        <a href="#"
                           class="update-vocabulary"
                           data-vocabulary-table-mode="true"
                           data-category-id="<?= $vocabulary_category_id; ?>"
                           data-vocabulary-id="<?= $vocabulary_id; ?>">
                            <?= $vocabulary_title; ?>
                        </a>
                    </td>
                    <td style="width: 25%;">
                        <?= $type_name; ?>
                        <? /*if ((int)$vocabulary['type'] === Vocabulary::TYPE_RANGE) { ?>
                            <br />(<?= $range_from; ?><?= $short_unit_string; ?> - <?= $range_to; ?><?= $short_unit_string; ?>
                                    , шаг: <?= $range_step; ?>)
                        <? }*/ ?>
                        <? if ((int)$vocabulary['type'] === Vocabulary::TYPE_SELECT) { ?>
                            <br /><a href="#" class="show-values update-vocabulary"
                                     data-vocabulary-table-mode="true"
                                     data-category-id="<?= $vocabulary_category_id; ?>"
                                     data-vocabulary-id="<?= $vocabulary_id; ?>">Показать значения.</a>
                        <? } ?>
                    </td>
                    <td style="text-align: center;">
                        <?= $full_unit_string; ?>
                    </td>
                    <td style="text-align: center;">
                        <div class="form-controls">
                            <label>
                                <input class="update-property"
                                       data-category-id="<?= $vocabulary_category_id; ?>"
                                       data-vocabulary-id="<?= $vocabulary_id; ?>"
                                       data-property-name="use_in_product_name"
                                       type="checkbox"
                                       <? if ($use_in_product_name) { ?>
                                           checked="checked"
                                       <? } ?>
                                       name="use_in_product_name[<?= $vocabulary_id; ?>]">
                            </label>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="form-controls">
                            <label>
                                <input class="update-property"
                                       data-category-id="<?= $vocabulary_category_id; ?>"
                                       data-vocabulary-id="<?= $vocabulary_id; ?>"
                                       data-property-name="use_only_vocabulary_name"
                                       type="checkbox"
                                    <? if ($use_only_vocabulary_name) { ?>
                                        checked="checked"
                                    <? } ?>
                                       name="use_only_vocabulary_name[<?= $vocabulary_id; ?>]">
                            </label>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="form-controls">
                            <label>
                                <input class="update-property"
                                       data-category-id="<?= $vocabulary_category_id; ?>"
                                       data-vocabulary-id="<?= $vocabulary_id; ?>"
                                       data-property-name="use_only_vocabulary_value"
                                       type="checkbox"
                                       <? if ($use_only_vocabulary_value) { ?>
                                            checked="checked"
                                       <? } ?>
                                       name="use_only_vocabulary_value[<?= $vocabulary_id; ?>]">
                            </label>
                        </div>
                    </td>
                    <td class="arrows">
                        <a href="#" class="up-vocabulary-position">&uarr;</a>&nbsp;
                        <a href="#" class="down-vocabulary-position">&darr;</a>
                    </td>
                    <td style="text-align: center;">
                        <?
                        // характеристика текущей категории.
                        if ($category->id === $vocabulary_category_id) {  ?>
                            <a href="#" class="remove-vocabulary for-table text-danger"
                               data-category-id="<?= $category->id; ?>"
                               data-vocabulary-id="<?= $vocabulary_id; ?>"
                               data-confirm="Вы уверены, что хотите удалить связь ?"
                            >
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        <? } else { // наследники (характеристики родительских категорий. )?>
                            <strong>наслед.</strong>
                        <? } ?>
                    </td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<?print Html::a('Привязать характеристику', '#', [
        'class' => 'btn btn-primary add-category-vocabulary',
        'data-category-id' => $category->id,
        'data-vocabulary-table-mode' => "true",
]);?>&nbsp;
<?php print Html::a('Добавить новую характеристику', ['vocabulary/update'], [
        'class' => 'btn btn-primary',
        'target' => '_blank',
]);?>
