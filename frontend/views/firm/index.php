<?
/**
 * Главная страница организаций (результаты поиска).
 * @var string $search_form
 * @var null|\common\models\firms\Firm $first_firm
 * @var \common\models\firms\Firm[] $firm_list
 * @var \yii\data\Pagination $pagination
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\modules\image\helpers\Image as ImageHelper;
use common\libs\Declension;

?>
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?= $search_form; ?>
        </div>
    </div>

    <?
    /**
     * Первая организация из списка.
     */
    if (!is_null($first_firm)) {
        echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/firm/parts/first_firm.php'), [
            'firm' => $first_firm,
        ]);
    }
    if (!empty($firm_list)) {
    ?>

    <div class="preview-items-block">
        <div class="preview-items preview-items_search-results preview-items_search-results-first">
            <? foreach ($firm_list as $firms) { ?>
            <div class="row">
                <?
                /** @var \common\models\firms\Firm[] $firms */
                foreach ($firms as $firm) {
                    // найденные товары организации.
                    /** @var \common\models\goods\Product[] $product_list */
                    $product_list = $firm->getProductByIds()
                        ->limit(4)
                        ->all();
                ?>
                <div class="col-xs-3 preview preview-small fade-menu-wrap">
                    <div class="row">
                        <div class="col-xs-6 col-md-6 col-lg-2 preview-thumbnail">
                            <?
                            if ($firm->image) {
                                $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 145, 145, ImageHelper::RESIZE_MODE_CROP);
                            ?>
                                <?= Html::img('/img/placeholders/145x145.png', [
                                    'class' => 'lazy',
                                    'data-original' => $image,
                                ]);?>
                                <noscript>
                                    <img src="<?= $image; ?>" alt="Логотип компании">
                                </noscript>
                            <?}?>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-4 preview-content">
                            <div class="preview-title-block">
                                <a href="<?= Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>" class="preview-title" title="<?= $firm->getTitle(); ?>"><?= $firm->getTitle(); ?></a>
                            </div>
                            <div class="preview-result">По запросу найдено
                                <a href="<?= Yii::$app->urlManager->createUrl(['product/index', 'query' => Yii::$app->request->get('query'), 'firm_id' => $firm->id]);?>">
                                    <?= Declension::number(count($firm->getProductIds()), 'наименование', 'наименования', 'наименований', true);?>
                                </a>
                            </div>
                            <ul class="preview-thumbs">
                                <? foreach ($product_list as $product) {
                                    $main_image = $product->getMainImage();
                                    $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 72, 72, ImageHelper::RESIZE_MODE_CROP);
                                ?>
                                <li>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]);?>">
                                        <?= Html::img('/img/placeholders/72x72.png', [
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
                    <div class="fade-menu fade-menu_preview">
                        <a role="button" href="#" data-id="<?= $firm->id; ?>" data-key="hold" class="action js-keeper">
                            <span class="action-icon action-icon-hold"></span>
                        </a>
                    </div>
                </div>
                <? } ?>
            </div>
            <? } ?>
        </div>
    </div>

<!--    <div class="banner-a2"></div>-->

    <? } ?>

    <div class="row">
        <div class="col-xs-6">
            <div class="row">
                <?
                /*
                 *  <div class="col-xs-4 col-xs-offset-1">
                        <div class="btn block btn-offset-search">Показать еще</div>
                    </div>
                 */
                ?>
                <div class="col-xs-6 col-sm-4 col-sm-offset-1">
                    <?
                    if (!is_null($pagination)) {
                        echo LinkPager::widget([
                            'pagination'    => $pagination,
                            'nextPageLabel' => 'Дальше',
                            'prevPageLabel' => 'Назад',
                        ]);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>