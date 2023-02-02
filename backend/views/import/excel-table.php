<?
/**
 * @var array $header_row
 * @var array $body_rows
 */

use yii\helpers\ArrayHelper;

$title_col_class = 'editable-col title-col success';
$price_col_class = 'editable-col price-col info';
$actions_col_class = 'actions-col';

$header_rows_count = count($header_row) + 2;
?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="price-actions">
            <button class="btn btn-success show-vocabulary-columns">Выбрать колонки характеристик.</button>
            <button disabled="disabled" class="btn btn-primary add-vocabulary-columns">Добавить характеристики к товарам.</button>
            <button class="btn btn-default product-category-relation">Привязать выбранные товары к категории.</button>
        </div>
    </div>
</div>

<table class="excel-table table table-hover table-striped table-bordered">
    <? if ($header_row) { ?>
        <thead>
            <tr>
                <td></td>
                <td>Категория</td>
                <? foreach ($header_row as $key => $item) {
                    if ($key == 'type' && $item === 'header') continue;
                    $col_class = $key==='title' ? $title_col_class : (($key==='price') ? $price_col_class : '');
                ?>
                    <td class="<?=$col_class;?>">
                        <strong class="head-col-content"><?=$item;?></strong>
                    </td>
                <? } ?>
                <td class="<?=$actions_col_class;?>">Действия</td>
            </tr>
        </thead>
    <? } ?>
    <tbody>
        <? foreach ($body_rows as $row_id => $body_row) {
            $row_type = ArrayHelper::getValue($body_row, 'type');
        ?>

            <!-- Строка товара. -->
            <? if ($row_type == 'product') { ?>
               <tr data-id="<?=$row_id;?>" class="product-row">
                   <td>
                       <label>
                           <input type="checkbox" class="product-checkbox" data-id="<?=$row_id;?>">
                       </label>
                   </td>
                   <!-- категория {-->
                   <td class="category-cell"> — </td>
                   <!-- категория }-->
                <? foreach ($body_row as $key => $col) {
                    if ($key === 'type') continue;
                    $col_class = $key==='title' ? $title_col_class : (($key==='price') ? $price_col_class : '');
                    $need_input = ($key==='title' || $key==='price');
                ?>
                    <td class="<?=$col_class;?>">
                        <? if ($need_input) { ?>
                            <label>
                                <input type="text" value="<?=$col;?>" />
                            </label>
                        <? } else {
                            print $col;
                        } ?>
                    </td>
                <? } ?>
                   <td>
                       <a data-id="<?=$row_id;?>" href="#" class="remove-row" title="Удалить">
                           <span class="glyphicon glyphicon-trash"></span>
                       </a>
                       <a data-id="<?=$row_id;?>" href="#" class="vocabularies" title="Характеристики">
                           <span class="glyphicon glyphicon-tasks"></span>
                       </a>
                   </td>
               </tr>
            <? } ?>

            <!-- Строка категории. -->
            <? if ($row_type == 'category') {
                $category_name = (isset($body_row[0])) ? $body_row[0] : '';
            ?>
                <tr>
                    <td colspan="<?=$header_rows_count;?>"><?=$category_name;?></td>
                </tr>
            <? } ?>

        <? } ?>
    </tbody>
</table>