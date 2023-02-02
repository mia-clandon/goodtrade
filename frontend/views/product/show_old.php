<?
/**
 * @var \common\models\goods\Product $product
 * @var string|null $referrer
 * @var \common\models\firms\Firm|null $firm
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use common\modules\image\helpers\Image as ImageHelper;
use frontend\components\widgets\SimilarProducts;
use frontend\components\widgets\CommercialRequest;

$has_firm = !is_null($firm);

//TODO: напрашивается в декоратор.
$main_image = $product->getMainImage();
$images = $product->getImages()->all();
?>
<div class="container">

    <div class="row item-container">
        <div class="col-xs-3">
            <? /*
            <div class="item-thumbnail">
                <? if ($main_image) {
                   $image = ImageHelper::i()->generateRelativeImageLink($main_image->image, 450);
                ?>
                    <?= Html::img('/img/placeholders/450x450.png', [
                        'class' => 'lazy',
                        'data-original' => $image,
                    ]);?>
                    <noscript>
                        <img src="<?= $image; ?>">
                    </noscript>
                <? } ?>
            </div>
            */ ?>

            <div class="item-thumbnail slider">
                <? if ($images) { ?>
                    <? foreach ($images as $image) {
                        $image = ImageHelper::i()->generateRelativeImageLink($image->image, 450);
                    ?>
                        <div class="slider-item">
                            <?= Html::img($image);?>
                        </div>
                    <? } ?>
                <? } ?>
            </div>

        </div>
        <div class="col-xs-3">
            <div class="item">
                <div class="item-header">
                    <? if (!is_null($referrer)) { ?>
                    <a href="<?=$referrer;?>" class="item-link">← К результатам поиска</a>
                    <? } ?>
                    <h2 class="item-title"><?= $product->getTitle(); ?></h2>
                    <div class="item-snippets">
                        <? if ($has_firm && $firm->isTopSeller()) { ?>
                        <div class="top-rated">Топовый продавец</div>
                        <? } ?>
                    </div>
                </div>
                <div class="item-content">
                    <? if ($product->price) { ?>
                        <div class="label"><?= $product->isPriceWitVAT()?'Цена с НДС':'Цена без НДС'?></div>

                        <?//TODO у нас пока нет истории изменения цены.
                        /*
                        <div class="item-price-date">Цена от: 12 октября 2016 г</div>
                        <?*/
                        ?>
                        <div class="item-price">
                            <span class="item-price-cost"><?= $product->getFormattedPrice(' ');?> тг.</span>
                            <span class="item-price-count"> / <?= $product->getUnitText(null, true);?>.</span>

                            <!--<span class="item-price-wholesale">Оптовая цена</span>-->
                        </div>
                    <? } else { ?>
                        <div class="item-price">
                            <span class="item-price-cost">Цена по уточнению</span>
                        </div>
                    <? } ?>
                    <div class="block">
                        <? if (!$product->isMine()) { ?>
                        <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="action js-keeper">
                            <span class="action-icon action-icon-hold"></span>
                            <span class="action-text">Отложить</span>
                        </a>
                        <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="action js-keeper">
                            <span class="action-icon action-icon-compare"></span>
                            <span class="action-text">Сравнить</span>
                        </a>
                        <? } ?>
                        <? /*
                        <div class="dropdown action">
                            <a href="#" role="button" class="dropdown-toggle">
                                <span class="action-icon action-icon-share"></span>
                                <span class="action-text">Поделиться</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-share">
                                <div class="dropdown-menu-head">
                                    <div class="dropdown-menu-title">Поделиться</div>
                                </div>
                                <div class="dropdown-menu-body">
                                    //TODO: виджет "поделиться"
                                    <ul class="share-list jq-share">
                                        <li class="share-list-item"><a
                                                href="http://vk.com/share.php?url=http://vk.com&amp;title=Заголовок&amp;description=Описание&amp;image=Ссылканакартинку&amp;noparse=true"
                                                target="_blank" class="jq-share-link"><i class="icon icon-vk"></i><span>Вконтакте</span></a>
                                        </li>
                                        <li class="share-list-item"><a
                                                href="https://www.facebook.com/sharer/sharer.php?u=asd"
                                                class="jq-share-link"><i
                                                    class="icon icon-facebook"></i><span>Facebook</span></a></li>
                                        <li class="share-list-item"><a
                                                href="https://www.linkedin.com/shareArticle?mini=true&amp;url=asdasd&amp;title=title&amp;summary=summary"
                                                target="_blank" class="jq-share-link"><i
                                                    class="icon icon-linkedin"></i><span>LinkedIn</span></a></li>
                                        <li class="share-list-item"><a href="whatsapp://send?text=The text to share!"
                                                                       data-action="share/whatsapp/share" target="_blank"><i
                                                    class="icon icon-whatsapp"></i><span>WhatsApp</span></a></li>
                                        <li class="share-list-item"><a
                                                href="mailto:someone@example.com?subject=This%20is%20the%20subject&amp;cc=someone_else@example.com&amp;body=This%20is%20the%20body"
                                                target="_blank"><i class="icon icon-mailto"></i><span>Mail</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        */ ?>
                    </div>
                    <? if (!$product->isMine()) { ?>
                    <div class="popup-dropdown">
                        <? if($product->hasMineCommercialRequest()) { ?>
                            <!-- Кнопка, если коммерческий запрос был отправлен не в данный момент, а до этого -->
                            <button class="btn btn-disabled "><span>Ожидается ответ (<?= $product->getCommercialRequestValidity()?> дней)</span></button>
<!--                            <button data-vertical-align="top" data-horizontal-align="left" data-resend="1" data-id="--><?//= $product->id;?><!--"-->
<!--                                    class="btn btn-link popup-toggle commercial-popup"><span>Отправить новый запрос</span>-->
<!--                            </button>-->
                        <? } else { ?>
                            <button data-vertical-align="top" data-horizontal-align="left" data-id="<?= $product->id;?>"
                                    class="btn btn-blue popup-toggle commercial-popup"><span>Коммерческий запрос</span>
                                <i class="icon icon-commerce-white"></i><i class="icon icon-complete"></i>
                            </button>
                        <? } ?>
                    </div>
                    <? } ?>
                </div>
                <div class="item-specs-container">
                    <div class="item-specs-container-inner">
                        <dl class="item-specs">
                            <? if ($has_firm && !empty($location = $firm->getLocation(false)) && null === $product->getLocation()) { ?>
                             <dt>Расположение:</dt>
                             <dd><?= $location; ?>
                                <?//TODO: <small>Расстояние 1450км Смотреть на карте</small>?>
                            </dd>
                            <? } if(null !== ($location = $product->getLocation())) { ?>
                                <dt>Расположение:</dt>
                                <dd><?= $location; ?>
                                </dd>
                            <? } ?>
                            <? if (count($product->getDeliveryTermsHelper()->getDeliveryTerms())) { ?>
                            <dt>Условия:</dt>
                            <dd><?= $product->getDeliveryTermsHelper()->getDeliveryTermsString(); ?>
                                <small>Таблица Incoterms</small>
                            </dd>
                            <? } ?>
                            <? if  ($capacity = $product->getCapacityString()) { ?>
                            <dt>Мощности:</dt>
                            <dd class="item-spec-light"><?= $capacity; ?></dd>
                            <? } ?>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4">
            <div class="tabs js-affix">
                <? if (!empty($product->getText()) || $product->getVocabularyHelper()->getValues()) { ?>
                <div class="tabs-header js-affix-float">
                    <? if (!empty($product->getText())) { ?>
                    <div class="tabs-btn is-active js-affix-tab"><a role="button" href="#js-tab1">Описание</a></div>
                    <? } ?>
                    <? if ($product->getVocabularyHelper()->getValues()) {?>
                    <div class="tabs-btn js-affix-tab"><a role="button" href="#js-tab2">Характеристики</a></div>
                    <? } ?>
                </div>
                <div class="tabs-content">
                    <? if (!empty($product->getText())) { ?>
                    <div class="text-collapse js-collapse js-affix-spy">
                        <div class="text-collapse-content js-collapse-content">
                            <h2 id="js-tab1" class="text-collapse-title">Коротко о <?= $product->getTitle(); //TODO: сделать склонение.?></h2>
                            <?= $product->getText(); ?>
                        </div>
                        <a role="button" href="#" class="text-collapse-btn js-collapse-toggle">Полное описание</a>
                    </div>
                    <? } ?>
                    <? if ($product->getVocabularyHelper()->getValues()) { ?>
                    <div class="text-collapse js-collapse js-affix-spy">
                        <div class="text-collapse-content js-collapse-content">
                            <h2 id="js-tab2" class="text-collapse-title">Технические характеристики</h2>
                            <table class="tech-specs">
                                <?= Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/vocabulary_terms.php'),[
                                        'product' => $product,
                                    ]);
                                ?>
                            </table>
                        </div>
                        <a role="button" href="#" class="text-collapse-btn js-collapse-toggle">Все характеристики</a>
                    </div>
                    <? } ?>
                </div>
                <? } ?>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="profile">
            <?= Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/firm_profile.php'),[
                    'firm' => $firm,
                ]);
            ?>
            </div>
        </div>
    </div>

    <?// похожие товары.?>
    <?= SimilarProducts::widget(['product_id' => $product->id]); ?>

    <?// нижняя строчка с коммерческим запросом.?>
    <?= CommercialRequest::widget(['product' => $product, 'type' => CommercialRequest::REQUEST_TYPE_BOTTOM]); ?>

    <?// модальное окно с коммерческим запросом.?>
    <?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL]); ?>
</div>