
<?
/**
 * Товары организации (на странице организации.)
 * @var integer $page
 * @var integer $product_count
 * @var integer $default_limit
 * @var array $product_list
 */

use yii\helpers\Html;

$page = isset($page) && is_integer($page) ? $page : 1;

if ($page == 1) {
    echo Html::input('hidden', 'page', $page);
}
?>

<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">
        <div class="preview-items-block">
            <div class="preview-items preview-items_search-results">
                <?
                    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/product_list.php'),[
                        'product_list' => $product_list,
                        'page' => $page,
                    ]);
                ?>
                <div class="load-product-container"></div>
            </div>
        </div>
    </div>
</div>

<? if ($default_limit*$page < $product_count) { ?>
<div class="row">
    <div class="col-xs-6">
        <div class="row">
            <div class="col-xs-4 col-xs-offset-1">
                <div class="btn block btn-offset-search show-more"
                     data-limit="<?= $default_limit ?>"
                     data-total-count="<?= $product_count ?>">
                    Показать еще
                </div>
            </div>
        </div>
    </div>
</div>
<? } ?>