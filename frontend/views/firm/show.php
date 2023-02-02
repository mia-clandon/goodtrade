<?
/**
 * @var \common\models\firms\Firm $firm
 * @var \common\models\Category[] $firm_categories
 * @var string|null $referrer
 * @var array $firm_product_categories
 * @var int $product_count
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use common\models\firms\Firm;
use common\modules\image\helpers\Image as ImageHelper;
use frontend\components\widgets\Callback;
use frontend\components\widgets\CommercialRequest;

echo Html::input('hidden', 'firm_id', $firm->id);

$main_image = $firm->getImage();

$image = null;
if($main_image) {
    $image = ImageHelper::i()->generateRelativeImageLink($main_image, 290, 290, ImageHelper::RESIZE_MODE_CROP);
}
else {
    /** @var \common\models\Category|null $first_activity */
    $first_activity = $firm->getCategories()->one();
    if ($first_activity) {
        $image = '/img/product_category_stubs/'. $first_activity->icon_class.'_big.png';
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-2">
            <div class="company-thumbnail">
                <?= Html::img('/img/placeholders/290x290.png', [
                    'class' => 'lazy',
                    'data-original' => $image,
                ]);?>
                <noscript>
                    <img src="<?= $image; ?>">
                </noscript>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="company-header">
                <? if (!is_null($referrer)) { ?>
                <div class="company-back">
                    <a href="<?=$referrer;?>" class="item-link">← Вернуться к товару</a>
                </div>
                <? } ?>
                <h2 class="company-title">
                    <?= $firm->getTitle(); ?>
                </h2>
                <div class="company-snippets">
                    <? if ($firm->isTopSeller()) { ?>
                    <div class="top-rated">Топовый продавец</div>
                    <? } ?>
                    <a role="button" href="#" data-id="<?= $firm->id ?>" data-key="favorite_company<?=\Yii::$app->user->id?>" class="action js-keeper">
                        <span class="action-icon action-icon-hold"></span>
                        <span class="action-text">Добавить в свой круг</span>
                    </a>
                </div>
                <? $location = $firm->getLocation(true); if (!empty($location)) { ?>
                <div class="company-location"><?= $location; ?></div>
                <? } ?>
                <?/*<div class="company-distance"><span>Расстояние 1450км</span><a>Смотреть на карте</a></div>*/?>
                <ul class="tags">
                    <? foreach ($firm_categories as $firm_category) { ?>
                    <li><a href="<?=Yii::$app->urlManager->createUrl(['category/show', 'id' => $firm_category->id])?>"><?= $firm_category->title;?></a></li>
                    <? } ?>
                </ul>
                <? if (!$firm->isMine() && 0) { ?>
                <div class="popup-dropdown call-me-order">
                    <button data-vertical-align="top" data-horizontal-align="left" data-id="<?= $firm->id; ?>" class="btn btn-blue popup-toggle callback-popup">
                        <span>Заказать обратный звонок</span>
                        <i class="icon icon-callme-white"></i>
                    </button>
                </div>
                <? } ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div id="tabs" class="tabs js-affix">
                <div class="tabs-header js-affix-float">
                    <div class="tabs-btn is-active js-affix-tab">
                        <a role="button" href="#js-tab1">Описание</a>
                    </div>
                    <div class="tabs-btn js-affix-tab">
                        <a role="button" href="#js-tab2">Каталог продукции<span><?=$product_count;?></span></a>
                    </div>
                </div>
                <div class="tabs-content row">
                    <div class="tabs-content-item is-visible col-lg-4">
                        <div class="text-collapse js-collapse js-affix-spy">
                            <h2 id="js-tab1" class="text-collapse-title">Коротко о компании</h2>
                            <div class="text-collapse-content js-collapse-content">
                                <p><?= $firm->getDescription(); ?></p>
                            </div>
                            <a role="button" href="#" class="text-collapse-btn js-collapse-toggle">Полное описание</a>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="banner-b2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <? if (count($firm_product_categories) > 0) { ?>
    <div class="row">
        <div class="col-lg-6">
            <h2 id="js-tab2" class="sub-title">Каталог</h2>
            <ul id="category-tabs" data-tabs-name="category-tabs" class="matches">
                <? foreach ($firm_product_categories as $product_category) { ?>
                <li data-category-id="<?= $product_category[Firm::FIELD_CATEGORY_ID]; ?>">
                    <a><?= $product_category[Firm::FIELD_CATEGORY_TITLE]; ?>
                        <span><?= $product_category[Firm::FIELD_PRODUCT_COUNT];?></span>
                    </a>
                </li>
                <? } ?>
            </ul>
        </div>
    </div>

    <? foreach ($firm_product_categories as $key => $product_category) { ?>
        <div id="category-tabs-<?=$key + 1;?>"></div>
    <? } ?>

    <? } ?>
</div>

<!-- Обратный звонок. -->
<?= Callback::widget(); ?>

<!-- Коммерческий запрос. -->
<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL]); ?>