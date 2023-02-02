<?
/**
 * Вывод избарнных компании и товаров
 * @var \common\models\goods\Product $products
 * @var bool $firm_id
 */

use common\modules\image\helpers\Image as ImageHelper;
use yii\helpers\Url;

?>
<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="favorites-block">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <div class="favorites-block__header">
                        <h2>Отложенные товары</h2>
                    </div>
                </div>
            </div>
            <div class="favorites-block__content">

                <div class="favorites-items-list">
                    <?foreach ($products as $product) {
                        $image = ImageHelper::i()->generateRelativeImageLink($product->getMainImage()->image, 140, 140);
                        ?>
                    <div class="row">
                        <div class="favorites-items-list__item">
                            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                                <div class="favorites-items-list__item-image"><img src="<?=$image?>"></div>
                            </div>
                            <div class="div col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <div class="favorites-items-list__item-title">
                                    <a href="<?=Url::to(['product/show', 'id' => $product->id])?>"><?= $product->getTitle(); ?></a>
                                </div>
                                <div class="favorites-items-list__item-price">
                                    <? if ($product->price) { ?>
                                        <div class="price"><?= $product->getFormattedPrice(' ');?> тг.</div>
                                        <div class="label">Базовая цена</div>
                                    <? } else { ?>
                                        <div class="price no-price">Цена по уточнению</div>
                                    <? } ?>
                                </div>
                                <div class="favorites-items-list__buttons">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle commercial-popup" data-id="<?= $product->id;?>">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i class="icon icon-complete"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>

            </div>
        </div>
    </div>
</div>