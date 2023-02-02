<?
/**
 * Выводит список маленьких товаров.
 * @var Product[] $product_list
 * @var \common\models\goods\Product $product
 * @var $first_category \common\models\Category
 * @var $activity \common\models\Category
 */

// Викентий: Перенёс вывод и часть логики из
// frontend/views/product/parts/new_product_list.php

use yii\helpers\Html;
use common\modules\image\helpers\Image as ImageHelper;
use common\models\goods\Product;
?>

<? if(!empty($product_list)) { ?>
<div class="block">
    <div class="block__title" data-type="affix-spy">
        <h2 id="similar-products" class="block__title-heading">Похожие товары</h2>
    </div>
    <div class="block__content">
        <div class="elements-grid">
            <div class="row">
<?
foreach ($product_list as $product) {

$first_category = $product->getCategories()->one();
$activity = $first_category->getActivity();

$product_url = Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]);
$category_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $first_category->id]);
$activity_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity->id]);

$main_image = $product->getMainImage();
if (null === $main_image) {
$main_image = '/img/product_category_stubs/' . $activity->icon_class . '.png';
} else {
switch ($product->mark_type) {
    case Product::MARK_TYPE_PRODUCT_BIG:
        {
            $main_image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 450);
        }
        break;
    case Product::MARK_TYPE_PRODUCT_SMALL:
        {
            $main_image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 200, 100);
        }
        break;
    case Product::MARK_TYPE_PRODUCT_NO:
        {
            $main_image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 140, 140);
        }
        break;
}
}
?>

<? if ($product->mark_type === Product::MARK_TYPE_PRODUCT_NO) { ?>
<div class="elements-grid__cell col-lg-4">
    <div class="row">
        <div class="col col_third col_no-right-gutter">
            <div class="elements-grid__cell-image">
                <?= Html::img('/img/placeholders/187x140.png', [
                    'class' => 'lazy',
                    'data-original' => $main_image,
                ]); ?>
                <noscript>
                    <img src="<?= $main_image; ?>"> <? //todo: alt.?>
                </noscript>
            </div>
        </div>
        <div class="col col_two-thirds">
            <div class="elements-grid__cell-content">
                <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]); ?>"
                   class="elements-grid__cell-title"
                   title="<?= $product->getTitle(); ?>"><?= $product->getTitle(); ?></a>
                <div class="breadcrumbs">
                    <div class="breadcrumbs__item">
                        <a href="<?= $activity_url; ?>"
                           class="breadcrumbs__item-icon breadcrumbs__item-icon_<?= $activity->icon_class; ?>"></a>
                    </div>
                    <div class="breadcrumbs__item">
                        <div class="breadcrumbs__item-title">
                            <a href="<?= $category_url; ?>"><?= $first_category->title; ?></a>
                        </div>
                    </div>
                </div>
                <div class="elements-grid__cell-bottom-container">
                    <? if (!$product->isPriceWitVAT()) { ?>
                        <div class="elements-grid__cell-vat">Цена без НДС</div>
                    <? } ?>
                    <div class="elements-grid__cell-price-container">
                        <div class="elements-grid__cell-price"><?= $product->getFormattedPrice(' '); ?>
                            тг.
                        </div>
                        <div class="elements-grid__cell-price-unit"><?= $product->getUnitDativeText('за'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="elements-grid__cell-footer-container">
        <div class="elements-grid__cell-bottom-container">
            <? if (!$product->isPriceWitVAT()) { ?>
                <div class="elements-grid__cell-vat">Цена без НДС</div>
            <? } ?>
            <div class="elements-grid__cell-price-container">
                <div class="elements-grid__cell-price"><?= $product->getFormattedPrice(' '); ?>тг.
                </div>
                <div class="elements-grid__cell-price-unit"><?= $product->getUnitDativeText('за'); ?></div>
            </div>
        </div>
        <? if (!$product->isMine()) { ?>
            <div class="elements-grid__cell-footer">
                <span class="modal-wrapper" data-type="popup-wrapper">
                    <button data-id="<?= $product->id; ?>"
                            data-version="new" data-action="popup-toggle"
                            class="button button_small button_primary popup-toggle">
                        <span class="button__text">Коммерческий запрос</span>
                        <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                    </button>
                </span>
                <button data-id="<?= $product->id; ?>" data-key="compare<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_left button__icon_scales-plus"></span>
                </button>
                <button data-id="<?= $product->id; ?>"
                        data-key="favorite_product<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                </button>
            </div>
        <? } ?>
    </div>
    <? if (!$product->isMine()) { ?>
        <div class="elements-grid__modal">
            <div class="row">
                <div class="col col_third col_no-right-gutter">
                    <div class="elements-grid__cell-image">
                        <?= Html::img('/img/placeholders/187x140.png', [
                            'class' => 'lazy',
                            'data-original' => $main_image,
                        ]); ?>
                        <noscript>
                            <img src="<?= $main_image; ?>"> <? //todo: alt.?>
                        </noscript>
                    </div>
                </div>
                <div class="col col_two-thirds">
                    <div class="elements-grid__cell-content">
                        <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]); ?>"
                           class="elements-grid__cell-title"
                           title="<?= $product->getTitle(); ?>"><?= $product->getTitle(); ?></a>
                        <div class="breadcrumbs">
                            <div class="breadcrumbs__item">
                                <a href="<?= $activity_url; ?>"
                                   class="breadcrumbs__item-icon breadcrumbs__item-icon_<?= $activity->icon_class; ?>"></a>
                            </div>
                            <div class="breadcrumbs__item">
                                <div class="breadcrumbs__item-title">
                                    <a href="<?= $category_url; ?>"><?= $first_category->title; ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="elements-grid__cell-bottom-container">
                            <? if (!$product->isPriceWitVAT()) { ?>
                                <div class="elements-grid__cell-vat">Цена без НДС</div>
                            <? } ?>
                            <div class="elements-grid__cell-price-container">
                                <div class="elements-grid__cell-price"><?= $product->getFormattedPrice(' '); ?>
                                    тг.
                                </div>
                                <div class="elements-grid__cell-price-unit"><?= $product->getUnitDativeText('за'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="elements-grid__cell-footer">
                <span class="modal-wrapper" data-type="popup-wrapper">
                    <button data-id="<?= $product->id; ?>"
                            data-version="new" data-action="popup-toggle"
                            class="button button_small button_primary popup-toggle">
                    <span class="button__text">Коммерческий запрос</span>
                    <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                    </button>
                </span>
                <button data-id="<?= $product->id; ?>"
                        data-key="compare<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_left button__icon_scales-plus"></span>
                    <span class="button__text">К сравнению</span>
                </button>
                <button data-id="<?= $product->id; ?>"
                        data-key="favorite_product<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                    <span class="button__text">Сохранить</span>
                </button>
            </div>
        </div>
    <? } ?>
</div>
<? } ?>

<? if ($product->mark_type === Product::MARK_TYPE_PRODUCT_BIG) { ?>
<div class="elements-grid__cell elements-grid__cell_promo-h2 col-lg-4">
    <div class="elements-grid__cell-content">
        <div class="elements-grid__bg">
            <?= Html::img('/img/placeholders/187x140.png', [
                'class' => 'lazy',
                'data-original' => $main_image,
            ]); ?>
            <noscript>
                <img src="<?= $main_image; ?>"> <? //todo: alt.?>
            </noscript>
        </div>
        <div class="breadcrumbs">
            <div class="breadcrumbs__item breadcrumbs__item_white">
                <a href="<?= $activity_url; ?>"
                   class="breadcrumbs__item-icon breadcrumbs__item-icon_<?= $activity->icon_class; ?>-white"></a>
            </div>
            <div class="breadcrumbs__item breadcrumbs__item_white">
                <div class="breadcrumbs__item-title breadcrumbs__item-title_white">
                    <a href="<?= $category_url; ?>"><?= $first_category->title; ?></a>
                </div>
            </div>
        </div>
        <div class="elements-grid__cell-bottom-container">
            <div class="row">
                <div class="col col_two-thirds">
                    <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]); ?>"
                       class="elements-grid__cell-title" title="<?= $product->getTitle(); ?>">
                        <?= $product->getTitle(); ?>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg col-lg_two-thirds">
                    <? if (!$product->isPriceWitVAT()) { ?>
                        <div class="elements-grid__cell-vat elements-grid__cell-vat_white">Цена без
                            НДС
                        </div>
                    <? } ?>
                    <div class="elements-grid__cell-price-container">
                        <div class="elements-grid__cell-price elements-grid__cell-price_white"><?= $product->getFormattedPrice(' '); ?>
                            тг.
                        </div>
                        <div class="elements-grid__cell-price-unit elements-grid__cell-price-unit_white"><?= $product->getUnitDativeText('за'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <? if (!$product->isMine()) { ?>
            <div class="elements-grid__cell-footer elements-grid__cell-footer_white-border">
                <span class="modal-wrapper" data-type="popup-wrapper">
                    <button data-id="<?= $product->id; ?>" data-version="new" data-action="popup-toggle"
                            class="button button_small button_primary popup-toggle">
                        <span class="button__text">Коммерческий запрос</span>
                        <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                    </button>
                </span>
                <button data-id="<?= $product->id; ?>" data-key="compare<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_scales-plus-white"></span>
                </button>
                <button data-id="<?= $product->id; ?>"
                        data-key="favorite_product<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_bookmark-plus-white"></span>
                </button>
            </div>
        <? } ?>
    </div>
</div>
<? } ?>

<? if ($product->mark_type === Product::MARK_TYPE_PRODUCT_SMALL) { ?>
<div class="elements-grid__cell elements-grid__cell_promo col-lg-4">
    <div class="elements-grid__cell-content">
        <div class="elements-grid__bg elements-grid__bg_half">
            <?= Html::img('/img/placeholders/187x140.png', [
                'class' => 'lazy',
                'data-original' => $main_image,
            ]); ?>
            <noscript>
                <img src="<?= $main_image; ?>"> <? //todo: alt.?>
            </noscript>
        </div>
        <div class="element-grid__cell-text-container">
            <div class="row">
                <div class="col col_two-thirds">
                    <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]); ?>"
                       class="elements-grid__cell-title" title="<?= $product->getTitle(); ?>">
                        <?= $product->getTitle(); ?></a>
                </div>
            </div>
            <div class="elements-grid__cell-bottom-container">
                <div class="row">
                    <div class="col-lg col-lg_two-thirds">
                        <? if (!$product->isPriceWitVAT()) { ?>
                            <div class="elements-grid__cell-vat elements-grid__cell-vat_white">Цена без
                                НДС
                            </div>
                        <? } ?>
                        <div class="elements-grid__cell-price-container">
                            <div class="elements-grid__cell-price elements-grid__cell-price_white"><?= $product->getFormattedPrice(' '); ?>
                                тг.
                            </div>
                            <div class="elements-grid__cell-price-unit elements-grid__cell-price-unit_white"><?= $product->getUnitDativeText('за'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <? if (!$product->isMine()) { ?>
            <div class="d-lg-none elements-grid__cell-footer elements-grid__cell-footer_white-border">
                <span class="modal-wrapper" data-type="popup-wrapper">
                    <button data-id="<?= $product->id; ?>" data-version="new" data-action="popup-toggle"
                            class="button button_small button_primary popup-toggle">
                        <span class="button__text">Коммерческий запрос</span>
                        <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                    </button>
                </span>
                <button data-id="<?= $product->id; ?>" data-key="compare<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_scales-plus-white"></span>
                </button>
                <button data-id="<?= $product->id; ?>"
                        data-key="favorite_product<?= \Yii::$app->user->id ?>"
                        class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_bookmark-plus-white"></span>
                </button>
            </div>
        <? } ?>
    </div>
    <? if (!$product->isMine()) { ?>
        <div class="elements-grid__modal">
            <div class="elements-grid__cell-content">
                <div class="elements-grid__bg elements-grid__bg_half">
                    <?= Html::img('/img/placeholders/187x140.png', [
                        'class' => 'lazy',
                        'data-original' => $main_image,
                    ]); ?>
                    <noscript>
                        <img src="<?= $main_image; ?>"> <? //todo: alt.?>
                    </noscript>
                </div>
                <div class="element-grid__cell-text-container">
                    <div class="row">
                        <div class="col col_two-thirds">
                            <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]); ?>"
                               class="elements-grid__cell-title" title="<?= $product->getTitle(); ?>">
                                <?= $product->getTitle(); ?></a>
                        </div>
                    </div>
                    <div class="elements-grid__cell-bottom-container">
                        <div class="row">
                            <div class="col col_two-thirds">
                                <? if (!$product->isPriceWitVAT()) { ?>
                                    <div class="elements-grid__cell-vat elements-grid__cell-vat_white">
                                        Цена без НДС
                                    </div>
                                <? } ?>
                                <div class="elements-grid__cell-price-container">
                                    <div class="elements-grid__cell-price elements-grid__cell-price_white"><?= $product->getFormattedPrice(' '); ?>
                                        тг.
                                    </div>
                                    <div class="elements-grid__cell-price-unit elements-grid__cell-price-unit_white"><?= $product->getUnitDativeText('за'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="elements-grid__cell-footer elements-grid__cell-footer_white-border">
                    <span class="modal-wrapper" data-type="popup-wrapper">
                        <button data-id="<?= $product->id; ?>" data-version="new" data-action="popup-toggle"
                                class="button button_small button_primary popup-toggle">
                            <span class="button__text">Коммерческий запрос</span>
                            <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                        </button>
                    </span>
                    <button data-id="<?= $product->id; ?>" data-key="compare<?= \Yii::$app->user->id ?>"
                            class="button button_small button_link button_action js-keeper">
                        <span class="button__icon button__icon_scales-plus-white"></span>
                    </button>
                    <button data-id="<?= $product->id; ?>"
                            data-key="favorite_product<?= \Yii::$app->user->id ?>"
                            class="button button_small button_link button_action js-keeper">
                        <span class="button__icon button__icon_bookmark-plus-white"></span>
                    </button>
                </div>
            </div>
        </div>
    <? } ?>
</div>
<? }

}
?>
            </div>
        </div>
    </div>
</div>
<? } ?>