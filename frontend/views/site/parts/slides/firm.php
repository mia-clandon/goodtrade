<?
/**
 * @var \common\models\MainSlider $slide
 * @var \common\models\firms\Firm $firm
 * @var bool $show_col
 */

use common\modules\image\helpers\Image as ImageHelper;
use frontend\components\lib\FirmProcessor;
use yii\helpers\Html;

/**
 * @var \common\models\Category $first_activity
 */
$first_activity = $firm->getCategories()->one();

$activity_icon = null;
$activity_title = null;
$activity_id = null;
$image = null;
if ($firm->image) {
    $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 140, 140);
}

if ($first_activity) {
    $activity_icon = $first_activity->icon_class;
    $activity_title = $first_activity->title;
    $activity_id = $first_activity->id;
    if(null === $image) {
        $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
    }
}

// найденные товары организации.
/** @var \common\models\goods\Product[] $product_list */
$product_list = FirmProcessor::i()
    ->setFirmId($firm->id)
    ->getProductList();
?>

<div class="cards-list__card <?= ($slide->getTypeNum() == 1 || $slide->getTypeNum() == 3) && $show_col ?'col-4':'col-8' ?>
                             <?= $slide->getTypeNum() == 1 || $slide->getTypeNum() == 2 ?'cards-list__card_borderless':'cards-list__card_tall' ?>"
                            style = "<?=!$show_col?'margin-bottom: 15px':''?>">
    <div class="cards-list__card-content" style="background-color:white!important;">
        <div class="cards-list__card-type">
            <div class="label label_primary-invert"><?= $slide->getCurrentTagText()?></div>
        </div>
        <div class="cards-list__card-content-bottom">
            <div class="elements-list">
                <div class="elements-list__item">
                    <div class="elements-list__item-image">
                        <?= Html::img('/img/placeholders/140x140.png', [
                            'class' => 'lazy',
                            'data-original' => $image,
                            'alt' => "<?= $firm->title?>",
                        ]);?>
                        <noscript>
                            <img src="<?= $image; ?>" alt = "<?= $firm->title?>">
                        </noscript>
                    </div>
                    <div class="elements-list__item-content">
                        <a href="<?=Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>" class="elements-list__item-title elements-list__item-title_one-line"><?= $firm->title?></a>
                        <div class="breadcrumbs">
                            <div class="breadcrumbs__item">
                                <a href="<?=Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity_id]);?>" class="breadcrumbs__item-icon breadcrumbs__item-icon_<?= $activity_icon ?>"></a>
                                <div class="breadcrumbs__item-title"><a href="<?=Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity_id]);?>"><?= $activity_title ?></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="elements-grid elements-grid_no-masonry">
                <div class="elements-grid__cell">
                    <div class="elements-grid__cell-content">
                        <div class="elements-grid__cell-thumbs">
                            <? foreach ($product_list as $product) {
                                $main_image = $product->getMainImage();
                                if ($main_image) {
                                    $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 72, 72);
                                    ?>
                                    <a class="elements-grid__cell-thumb" href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]);?>">
                                        <img src="<?= $image; ?>"  alt="Фото товара компании">
                                    </a>
                                <? }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>