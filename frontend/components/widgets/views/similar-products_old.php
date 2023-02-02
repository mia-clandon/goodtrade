<?
/**
 * Выводит список маленьких товаров.
 * @var array $products
 * @var \common\models\goods\Product[] $product_list
 */

use yii\helpers\Html;
use common\modules\image\helpers\Image as ImageHelper;

//TODO: взять вывод из frontend/views/product/parts/product-list.php

?>

<? if (!empty($products)) { ?>

<div class="preview-items-block">

    <div class="row">
        <div class="col-xs-6">
            <div class="sub-title">Возможно вас заинтересуют эти предложения</div>
        </div>
    </div>

    <div class="preview-items">
        <? foreach ($products as $product_list) { ?>
        <div class="row">
            <? foreach ($product_list as $product) { ?>
            <div class="col-xs-3 preview preview-small fade-menu-wrap">
                <div class="row">
                    <div class="col-xs-2 preview-thumbnail">
                        <?
                        $image = $product->getMainImage();
                        if ($image) {
                            $image = ImageHelper::i()->generateRelativeImageLink($image->image, 145);
                        ?>
                            <?= Html::img('/img/placeholders/145x145.png', [
                                'class' => 'lazy',
                                'data-original' => $image,
                            ]);?>
                            <noscript>
                                <img src="<?= $image; ?>">
                            </noscript>
                        <? } ?>
                    </div>
                    <div class="col-xs-4 preview-content">
                        <div class="preview-title-block">
                            <?=Html::a($product->getTitle(), Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]), [
                                'class' => 'preview-title',
                                'title' => $product->getTitle(),
                            ]);?>
                        </div>
                        <div class="preview-price-block">
                            <div class="price"><?=$product->getFormattedPrice(' '); ?> тг.</div>
                            <?/* if ($product->isPriceWitVAT()) { ?>
                            <div class="label">Базовая цена</div>
                            <? } */?>
                            <div class="label">Базовая цена</div>
                        </div>
                        <div class="preview-offer">
                            <? if (!$product->isMine()) { ?>
                            <div class="popup-dropdown">
                                <div data-id="<?= $product->id; ?>" class="btn btn-sm btn-lowercase popup-toggle commercial-popup">
                                    <span>Коммерческий запрос</span>
                                    <i class="icon icon-sm-commerce"></i>
                                    <i class="icon icon-complete"></i>
                                </div>
                            </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
                <? if (!$product->isMine()) { ?>
                <div class="fade-menu fade-menu_preview">
                    <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="action js-keeper"><span
                            class="action-icon action-icon-hold"></span></a>
                    <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="action js-keeper"><span
                            class="action-icon action-icon-compare"></span></a>
                </div>
                <? } ?>
            </div>
            <? } ?>
        </div>
        <? } ?>
    </div>
</div>
<? } ?>