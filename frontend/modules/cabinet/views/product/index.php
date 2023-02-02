<?
/**
 * Мои товары в кабинете.
 * @var integer $product_count
 * @var string $search_form
 * @var array $found_product
 * @var \common\models\PriceQueue[] $price_list
 * @author Артём Широких kowapssupport@gmail.com
 */

use common\libs\Declension;
use yii\helpers\Html;
use common\modules\image\helpers\Image;

?>
<div class="container">
    <div class="cabinet">
        <div class="row">
            <div class="col-xs-6">
                <h2 class="sub-title">Ваши товары
                    <span><?= $product_count.' '.Declension::number($product_count, 'товар', 'товара', 'товаров');?></span>
                </h2>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?= $search_form; ?>
            </div>
        </div>

        <div id="tab-product-list" class="preview-items tab-wrapper">
            <? foreach ($found_product as $row) { ?>
            <div class="row">

                <?
                /** @var \common\models\goods\Product $product */
                foreach ($row as $product) {
                    if (!empty($product)) {
                ?>
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail">
                            <?
                                $main_image = $product->getMainImage();
                                if ($main_image) {
                            ?>
                                <?= Html::img(Image::i()->generateRelativeImageLink($main_image->image, 145));?>
                            <? } ?>
                            </div>
                            <div class="col-xs-4 preview-content">
                                <a class="preview-title"
                                   href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id])?>">
                                    <?= $product->getTitle();?>
                                </a>
                                <? if ($product->price) { ?>
                                <div class="price"><?= $product->getFormattedPrice(' ');?> тг.</div>
                                <? } ?>
                                <? if ($product->isPriceWitVAT()) { ?>
                                <div class="label">Базовая цена</div>
                                <? } ?>
                                <div class="preview-item-button-block-outer">
                                    <div class="preview-item-button-block-inner">
                                        <div class="preview-item-button-block">
                                            <a href="#" class="btn btn-sm btn-lowercase"><span>Редактировать</span></a>
                                            <a href="#" class="preview-item-button-link">Удалить</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <? } else { ?>
                    <div class="col-xs-3 preview preview-small">
                        <a href="<?=Yii::$app->urlManager->createUrl(['cabinet/product/add-price']);?>" class="preview-add">
                            <i class="icon icon-plus-lg"></i>
                            <span>Добавить товар</span>
                        </a>
                    </div>
                <? } ?>
            <? } ?>
            </div>
            <? } ?>
        </div>
        <div id="tab-price-list" class="tab-wrapper" style="display: none;">
            <table class="price-list-table">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Название файла прайс листа</td>
                        <td>Статус</td>
                    </tr>
                </thead>
                <tbody>
                <? foreach ($price_list as $item) {?>
                    <tr>
                        <td><?= $item->id;?></td>
                        <td><?= $item->getFileName();?></td>
                        <td><?= $item->getStatusName();?></td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>