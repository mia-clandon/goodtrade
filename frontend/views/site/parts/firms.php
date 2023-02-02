<?
/**
 * @var \common\models\firms\Firm[] $firms
 */

use common\modules\image\helpers\Image as ImageHelper;
use yii\helpers\Html;

//todo: получить данные $firms 1м запросом.
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
    }
    else {
        /** @var \common\models\Category|null $first_activity */
        $first_activity = $firm->getCategories()->one();

        if ($first_activity) {
            $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
        }
    }
?>
    <div class="elements-list__item">
        <div class="elements-list__item-image">
            <?= Html::img('/img/placeholders/140x140.png', [
                'class' => 'lazy',
                'data-original' => $image,
                'alt' => "Логотип компании",
            ]);?>
            <noscript>
                <img src="<?= $image; ?>" alt = "Логотип компании">
            </noscript>
        </div>
        <div class="elements-list__item-content">
            <a href="<?=Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>" class="elements-list__item-title"><?= $firm->getTitle() ?></a>
            <div class="breadcrumbs">
                <div class="breadcrumbs__item">
                    <a href="<?=$activity_url?>" class="breadcrumbs__item-icon breadcrumbs__item-icon_<?= $activity_icon ?>"></a>
                    <div class="breadcrumbs__item-title"><a href="<?=$category_url?>"><?= $category_title ?></a></div>
                </div>
            </div>
        </div>
    </div>
<? } ?>