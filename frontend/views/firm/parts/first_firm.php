<?
/**
 * Рендерит первую строку(большую) с организацией.
 * @var \common\models\firms\Firm $firm
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use common\modules\image\helpers\Image as ImageHelper;
use common\libs\Declension;

// найденные товары организации.
/** @var \common\models\goods\Product[] $product_list */
$product_list = $firm->getProductByIds()
    ->limit(4)
    ->all();

?>

<div class="row preview-wrap preview-wrap_firm">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
        <div class="preview-thumbnail">
            <?
                if ($firm->image) {
                $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 290, 290, ImageHelper::RESIZE_MODE_CROP);
            ?>
                <?= Html::img('/img/placeholders/290x290.png', [
                    'class' => 'lazy',
                    'data-original' => $image,
                ]);?>
                <noscript>
                    <img src="<?= $image; ?>" alt="Логотип компании">
                </noscript>
            <?}?>
        </div>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4">
        <div class="preview-content">
            <h2 class="preview-title">
                <a href="<?= Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>" title="<?= $firm->getTitle(); ?>"><?= $firm->getTitle(); ?></a>
            </h2>
            <div class="preview-snippets">
                <? if ($firm->isTopSeller()) { ?>
                <div class="top-rated">Топовый продавец</div>
                <? } ?>
                <? /*
                <a role="button" href="#" data-id="<?= $firm->id; ?>" data-key="hold" class="action js-keeper">
                    <span class="action-icon action-icon-hold"></span>
                    <span class="action-text">Добавить в свой круг</span>
                </a>
                */ ?>
            </div>
            <div class="preview-thumbs-container">
                <div class="preview-thumbs-inner">
                    <div class="preview-result">
                        <span>По запросу найдено</span>
                        <a href="<?= Yii::$app->urlManager->createUrl(['product/index', 'query' => Yii::$app->request->get('query'), 'firm_id' => $firm->id]);?>">
                            <?= Declension::number(count($firm->getProductIds()), 'наименование', 'наименования', 'наименований', true);?>
                        </a>
                    </div>
                    <ul class="preview-thumbs">
                        <? foreach ($product_list as $product) {
                            $main_image = $product->getMainImage();
                            $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 152, 152, ImageHelper::RESIZE_MODE_CROP);
                        ?>
                            <li>
                                <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]);?>">
                                    <?= Html::img('/img/placeholders/152x152.png', [
                                        'class' => 'lazy',
                                        'data-original' => $image,
                                    ]);?>
                                    <noscript>
                                        <img src="<?= $image; ?>">
                                    </noscript>
                                </a>
                            </li>
                        <? } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>