<?
/**
 * Главная страница результатов поиска.
 * @var string $search_form
 * @var \common\models\goods\Product $first_product
 * @var \common\models\goods\Product[] $product_list
 * @var \yii\data\Pagination $pagination
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\widgets\LinkPager;
use frontend\components\widgets\CommercialRequest;

?>

<div class="container">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?= $search_form; ?>
        </div>
    </div>

    <?
    /**
     * Первый товар из списка товаров.
     */
    if (!is_null($first_product)) {
        echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/first_product.php'), [
            'product' => $first_product,
        ]);
    }
    if (!empty($product_list)) {
    ?>

    <div class="preview-items-block">
        <div class="preview-items preview-items_search-results preview-items_have-top-border">
            <?
                echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/product_list.php'), [
                    'product_list' => $product_list,
                ]);
            ?>
        </div>

<!--        <div class="banner-a2"></div>-->

        <div class="row">
            <div class="col-xs-6">
                <div class="row">
                    <?/*
                    <div class="col-xs-4 col-xs-offset-1">
                        <div class="btn block btn-offset-search">Показать еще</div>
                    </div>
                    */?>
                    <div class="col-xs-6 col-sm-4 col-sm-offset-1">
                        <?
                        if (!is_null($pagination)) {
                            echo LinkPager::widget([
                                'pagination'    => $pagination,
                                'nextPageLabel' => 'Дальше',
                                'prevPageLabel' => 'Назад',
                            ]);
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
    <? } ?>
</div>

<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL]); ?>