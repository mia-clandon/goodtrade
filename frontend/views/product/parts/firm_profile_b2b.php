<?
/**
 * Блок с профилем организации.
 * @var \common\models\firms\Firm $firm
 * @var \common\models\Location $city
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use common\modules\image\helpers\Image as ImageHelper;

// todo: подобные конструкции можно вынести в helper, к примеру FirmHelper.php, или декоратор FirmProfileDecorator.php
$image = null;
$city = null;
if ($firm) {
    /** @var \common\models\Category $first_category */
    $first_category = $firm->getCategories()->one();
    $activity = $first_category->getActivity();

    $category_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $first_category->id]);
    $activity_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity->id]);

    $city = $firm->getCity()->one();

    if ($firm->image) {
        $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 226);
    }
    else {
        /** @var \common\models\Category|null $first_activity */
        $first_activity = $firm->getCategories()->one();
        if ($first_activity) {
            $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
        }
    }
}

if ($firm and !isset($type)) { ?>
    <div class="small-block">
        <div class="small-block__content">
            <div class="product-firm">
                <div class="product-firm__logo"><? if ($image) { echo Html::img($image); } ?></div>
                <div class="product-firm__info-row">
                    <a href="<?= Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>"
                       title="<?= $firm->getTitle(); ?>"
                       class="product-firm__title"><?= $firm->getTitle(); ?></a>
                    <? if ($city) { ?>
                        <div class="product-firm__location"><?= $city->title; ?></div>
                    <?  } ?>
                </div>
                <div class="product-firm__info-row">
                    <? if ($firm->isTopSeller()) { ?>
                        <div class="top-rated">Топовый продавец</div>
                    <? } ?>
                    <div class="breadcrumbs">
                        <div class="breadcrumbs__item">
                            <a href="<?=$activity_url;?>" class="breadcrumbs__item-icon breadcrumbs__item-icon_<?=$activity->icon_class;?>"></a>
                            <div class="breadcrumbs__item-title">
                                <a href="<?=$category_url;?>"><?= $first_category->title; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <? //TODO: реализовать добавление компании в избранное
                /*<button data-id="<?= $firm->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="button button_small button_link button_action js-keeper">
                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                    <span class="button__text">Добавить в избранное</span>
                </button>*/
                ?>
            </div>
        </div>
    </div>
<? }
else if ($firm and $type === "small") { ?>
<div class="small-block small-block_bordered">
    <div class="small-block__title">Продавец</div>
    <div class="small-block__content">
        <div class="elements-list">
            <div class="elements-list__item">
                <div class="elements-list__item-image">
                    <? if ($image) { echo Html::img($image); } ?>
                </div>
                <div class="elements-list__item-content">
                    <a href="<?= Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>"
                       title="<?= $firm->getTitle(); ?>"
                       class="elements-list__item-title"><?= $firm->getTitle(); ?></a>
                    <div class="elements-list__item-row">
                        <? if ($firm->isTopSeller()) { ?>
                            <div class="top-rated"></div>
                        <? } ?>
                        <div class="breadcrumbs">
                            <div class="breadcrumbs__item">
                                <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_construction"></a>
                                <div class="breadcrumbs__item-title"><a href="#">Строительство</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="elements-list__item-buttons-row">
                        <button data-id="<?= $firm->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="button button_small button_link button_action js-keeper">
                            <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                            <span class="button__text">Добавить в избранное</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<? } ?>