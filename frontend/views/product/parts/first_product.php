<?
/**
 * Рендерит первый товар из списка товаров.
 * @var \common\models\goods\Product $product
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\goods\Product;
use common\modules\image\helpers\Image as ImageHelper;

/** @var \common\models\firms\Firm $firm */
$firm = $product->getFirm()->one();
$has_firm = !is_null($firm);

$main_image = $product->getMainImage();
$vocabulary_terms = $product->getVocabularyTermsArray();

?>

<div class="row preview-wrap">

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
        <div class="preview-thumbnail">
            <? if ($main_image) {
                $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 290);
            ?>
                <?= Html::img('/img/placeholders/290x290.png', [
                    'class' => 'lazy',
                    'data-original' => $image,
                ]);?>
                <noscript>
                    <img src="<?= $image; ?>">
                </noscript>
            <? } ?>
        </div>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
        <div class="row">
            <div class="col-xs-6 col-sm-6">
                <div class="preview">
                    <div class="preview-content preview-margin-off">
                        <h2 class="preview-title">
                            <?= Html::a($product->getTitle(), Url::to(['product/show', 'id' => $product->id]));?>
                        </h2>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4">
                                <!--<dl class="preview-specs">
                                    <?/* foreach ($vocabulary_terms as $vocabulary_term) {
                                        $vocabulary = $vocabulary_term[Product::VOCABULARY_KEY];
                                        $terms = $vocabulary_term[Product::TERMS_KEY];
                                    */?>
                                    <dt><?/*= $vocabulary[Product::PROP_VOCABULARY_TITLE]; */?></dt>
                                    <dd><?/*= implode(', ', $terms); */?></dd>
                                    <?/* } */?>
                                </dl>-->

                                <ul class="preview-specs">
                                    <? foreach ($vocabulary_terms as $vocabulary_term) {
                                        $vocabulary = $vocabulary_term[Product::VOCABULARY_KEY];
                                        $terms = $vocabulary_term[Product::TERMS_KEY];
                                    ?>
                                    <li class="preview-specs__item">
                                        <span class="preview-specs__item-name"><?= $vocabulary[Product::PROP_VOCABULARY_TITLE]; ?></span> &mdash; <span class="preview-specs__item-value"><?= implode(', ', $terms); ?></span>
                                    </li>
                                    <? } ?>
                                </ul>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-2">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-6 preview-snippets">
                                        <div class="price"><?= $product->getFormattedPrice(' ');?> тг.</div>
                                        <? /*if ($product->isPriceWitVAT()) { ?>
                                        <div class="label">Базовая цена</div>
                                        <? } */ ?>
                                        <div class="label">Базовая цена</div>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-6 preview-actions">
                                        <div class="block preview-actions-block">
                                            <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="hold" class="action js-keeper">
                                                <span class="action-icon action-icon-hold"></span>
                                                <span class="action-text">Отложить</span>
                                            </a>
                                            <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="action js-keeper">
                                                <span class="action-icon action-icon-compare"></span>
                                                <span class="action-text">Сравнить</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <? if (!$product->isMine()) { ?>
                            <div class="preview-buttons-container">
                                <div class="preview-buttons-inner">
                                    <div class="preview-buttons popup-dropdown">
                                        <div data-id="<?= $product->id; ?>" class="btn btn-blue popup-toggle commercial-popup">
                                            <span>Коммерческий запрос</span>
                                            <i class="icon icon-commerce-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                        <? /* ?>
                        <div data-align="top" class="btn-group popup-dropdown">
                          <button class="btn"><i class="icon icon-callme"></i></button>
                          <button class="btn"><i class="icon icon-handshake"></i></button>
                        </div>
                        <? */ ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-1">
        <div class="profile preview-profile">
        <?
            if ($has_firm) {
                echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/firm_profile.php'), [
                    'firm' => $firm,
                ]);
            }
        ?>
        </div>
    </div>
</div>