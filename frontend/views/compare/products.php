<?
/**
 * Результаты поиска
 * @var \common\models\goods\Product[] $products
 * @var int $product_count
 * @var int $product_limit
 * @var int $product_offset
 * @author yerganat
 */

use common\modules\image\helpers\Image as ImageHelper;
use yii\helpers\Url;

foreach ($products as $product) {
    /** @var \common\models\Category $category */
    $category = $product->getCategories()->one();
    $image = $product->getMainImage();
    if ($image) {
        $image = ImageHelper::i()->generateRelativeImageLink($image->image, 140, 140, ImageHelper::RESIZE_MODE_CROP);
    }
    else {
        /** @var \common\models\Category $activity */
        if ($category && $activity = $category->getActivity()) {
            $image = '/img/product_category_stubs/'. $activity->icon_class.'.png';
        }
    }

    $first_Category = $product->getCategories()->one();
    ?>
    <div class="row">
        <div class="preview preview-small preview-small_comparison-search">
            <div class="row">
                <div class="col-xs-2 preview-thumbnail"><img src="<?= $image ?>"></div>
                <div class="col-xs-4 preview-content">
                    <div class="preview-title-block">
                        <a href="<?=Url::to(['product/show', 'id' => $product->id])?>" class="preview-title"><?=$product->getTitle()?></a>
                    </div>
                    <div class="preview-price-block">
                        <? if ($product->price) { ?>
                            <div class="price"><?= $product->getFormattedPrice(' ');?> тг.</div>
                        <? } else { ?>
                            <div class="price no-price">Цена по уточнению</div>
                        <? } ?>
                    </div>
                    <div class="preview-offer">
                        <button data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="btn btn-sm btn-lowercase action js-keeper">Добавить к сравнению
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? } ?>

<? if($product_offset == 0 && $product_limit < $product_count) { ?>
<div class="row more_suggest_area">
    <button class="btn more_suggest_product" data-offset="<?=$product_limit?>" data-limit="<?=$product_limit?>" data-count="<?=$product_count?>">ПОКАЗАТЬ ЕЩЕ
    </button>
</div>
<? } ?>
