<?
/**
 * Блок с популярными организациями
 * @var \common\models\firms\Firm[] $firms
 * @var \common\models\Category $category
 * @author yerganat
 */

use common\modules\image\helpers\Image as ImageHelper;
use frontend\components\lib\FirmProcessor;
use yii\helpers\Html;

foreach ($firms as $firm) {
    /**
     * @var \common\models\Category $first_category
     * @var \common\models\Category $activity
     */
    $activity_icon = '';
    $category_title = '';
    $first_category = $firm->getCategories()->one();

    $category_url = '';
    $activity_url = '';
    if ($first_category) {
        $activity = $first_category->getActivity();
        $activity_icon = $activity->icon_class;
        $category_title = $first_category->title;

        $category_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $first_category->id]);
        $activity_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity->id]);
    }

    $image = null;
    if ($firm->getImage()) {
        $image = ImageHelper::i()->generateRelativeImageLink($firm->getImage(), 140, 140);
    } else {
        /** @var \common\models\Category|null $first_activity */
        $first_activity = $firm->getCategories()->one();

        if ($first_activity) {
            $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
        }
    }

    // найденные товары организации.
    /** @var \common\models\goods\Product[] $product_list */
    $product_list = FirmProcessor::i()
        ->setFirmId($firm->id)
        ->getProductList();
 ?>
<div class="elements-grid__cell col-lg-4">
    <div class="row">
        <div class="col col_third col_no-right-gutter">
            <div class="elements-grid__cell-image">
                <?= Html::img('/img/placeholders/140x140.png', [
                    'class' => 'lazy',
                    'data-original' => $image,
                    'alt' => "Логотип компании",
                ]);?>
                <noscript>
                    <img src="<?= $image; ?>" alt = "Логотип компании">
                </noscript>
            </div>
        </div>
        <div class="col col_two-thirds">
            <div class="elements-grid__cell-content">
                <a href="<?=Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id])?>" class="elements-grid__cell-title" title="<?=$firm->getTitle()?>"><?=$firm->getTitle()?></a>
                <div class="breadcrumbs">
                    <div class="breadcrumbs__item">
                        <a href="<?=$activity_url?>" class="breadcrumbs__item-icon breadcrumbs__item-icon_<?= $activity_icon ?>"></a>
                        <div class="breadcrumbs__item-title">
                            <a href="<?= $category_url ?>"><?= $category_title ?></a>
                        </div>
                    </div>
                </div>
                <div class="elements-grid__cell-thumbs">
                    <? foreach ($product_list as $product) {
                        $main_image = $product->getMainImage();
                        if ($main_image) {
                            $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 72, 72);
                        }
                        ?>
                        <a class="elements-grid__cell-thumb" href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]);?>">
                            <img src="<?= $image; ?>"  alt="Фото товара компании">
                        </a>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="elements-grid__cell-footer-container">
        <div class="elements-grid__cell-thumbs">
            <? foreach ($product_list as $product) {
                $main_image = $product->getMainImage();
                if ($main_image) {
                    $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 72, 72);
                }
                ?>
                    <a class="elements-grid__cell-thumb" href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]);?>">
                        <img src="<?= $image; ?>"  alt="Фото товара компании">
                    </a>
            <? } ?>
        </div>
    </div>
</div>
<? } ?>