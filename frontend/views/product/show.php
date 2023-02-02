<?
/**
 * @var \common\models\goods\Product $product
 * @var string|null $referrer
 * @var \common\models\firms\Firm|null $firm
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use frontend\components\widgets\SimilarProducts;
use frontend\components\widgets\CommercialRequest;

$has_firm = !is_null($firm);

//TODO: напрашивается в декоратор.
$main_image = $product->getMainImage();
$images = $product->getImages()->all();

/** @var \common\models\Category $first_category */
$first_category = $product->getCategories()->one();
$activity = $first_category->getActivity();

$category_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $first_category->id]);
$activity_url = Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity->id]);
?>
    <main>
        <div class="container container_main">
            <div class="row">
                <div class="col-lg-4">
                    <div class="product-photo-slider">
                        <? if ($images) { ?>
                            <?
                            /** @var \common\models\goods\Images $image */
                            foreach ($images as $image) {
                                if ($image = $image->getImage(570)) {
                                    ?>
                                    <div class="product-photo-slider__item">
                                        <?= Html::img($image); ?>
                                    </div>
                                <? }
                            }?>
                        <? } ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="product-info">
                        <div class="product-control-buttons d-sm-none d-lg-block">
                            <? if (!is_null($referrer)) { ?>
                                <a href="<?= $referrer; ?>" class="button button_small button_link">
                                    <span class="button__icon button__icon_arrow-left"></span>
                                    <span class="button__text">Вернуться назад</span>
                                </a>
                            <? } ?>
                            <? if (!$product->isMine()) { ?>
                                <a href="#" data-id="<?= $product->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="button button_small button_link button_action js-keeper">
                                    <span class="button__icon button__icon_bookmark-plus"></span>
                                    <span class="button__text">Добавить в избранное</span>
                                </a>
                                <a href="#" data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="button button_small button_link button_action js-keeper">
                                    <span class="button__icon button__icon_scales-plus"></span>
                                    <span class="button__text">Добавить к сравнению</span>
                                </a>
                            <? } ?>
                        </div>
                        <h2 class="product-title d-sm-none d-lg-block"><?= $product->getTitle(); ?></h2>
                        <div class="product-subdata">
                            <div class="breadcrumbs">
                                <div class="breadcrumbs__item">
                                    <a href="<?=$activity_url;?>" class="breadcrumbs__item-icon breadcrumbs__item-icon_<?=$activity->icon_class;?>"></a>
                                </div>
                                <div class="breadcrumbs__item">
                                    <div class="breadcrumbs__item-title">
                                        <a href="<?=$category_url;?>"><?= $first_category->title; ?></a>
                                    </div>
                                </div>
                            </div>
                            <? if ($has_firm && $firm->isTopSeller()) { ?>
                                <div class="top-rated d-sm-none d-lg-flex">Топовый продавец</div>
                            <? } ?>
                        </div>
                        <h2 class="product-title d-lg-none"><?= $product->getTitle(); ?></h2>
                        <div class="product-price">
                            <div class="product-price__sub-data">
                                <? //TODO: у нас пока нет истории изменения цен
                                /*
                                <div class="label label_success-invert">&mdash; 23%</div>
                                */
                                ?>
                                <div class="product-price__vat">Базовая цена <?= $product->isPriceWitVAT() ? 'с НДС' : 'без НДС'; ?></div>
                            </div>
                            <div class="product-price__price-row">
                                <? if ($product->price) { ?>
                                    <div class="product-price__value"><?= $product->getFormattedPrice(' '); ?> тг.</div>
                                    <div class="product-price__unit"><?= $product->getUnitDativeText("за "); ?></div>
                                <? } else { ?>
                                    <div class="product-price__value">Цена по уточнению</div>
                                <? } ?>
                            </div>
                            <? //TODO: у нас пока нет истории изменения цен
                            /*
                            <div class="product-price__history-row">
                                <div class="product-price__date">От 12 октября 2016 г.</div>
                                <a href="#" class="product-price__history-link">История обновления цен</a>
                            </div>
                            */
                            ?>
                        </div>
                        <div class="product-key-actions">
                            <span class="modal-wrapper" data-type="popup-wrapper">
                            <? if($product->hasMineCommercialRequest()) { ?>
                                <!-- Кнопка, если коммерческий запрос был отправлен не в данный момент, а до этого -->
                                <button class="button button_disabled">
                                    <span class="button__text">Ожидается ответ (<?= $product->getCommercialRequestValidity()?> дней)</span>
                                </button>
                            <? } else { ?>
                                <button data-id="<?= $product->id; ?>" data-version="new" data-action="popup-toggle" class="button button_primary">
                                    <span class="button__text">Коммерческий запрос</span>
                                    <span class="button__icon button__icon_bill-send-white"></span>
                                </button>
                            <? } ?>
                            </span>
                        </div>
                        <div class="product-control-buttons d-lg-none">
                            <? if (!$product->isMine()) { ?>
                                <a href="#" data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="button button_small button_link button_action js-keeper">
                                    <span class="button__icon button__icon_scales-plus"></span>
                                    <span class="button__text">К сравнению</span>
                                </a>
                                <a href="#" data-id="<?= $product->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="button button_small button_link button_action js-keeper">
                                    <span class="button__icon button__icon_bookmark-plus"></span>
                                    <span class="button__text">Сохранить</span>
                                </a>
                            <? } ?>
                        </div>
                        <div class="product-primary-specs">
                            <? if ($has_firm && !empty($location = $firm->getLocation(false)) && null === $product->getLocation()) { ?>
                                <div class="product-primary-specs__row row">
                                    <div class="product-primary-specs__name col-lg-2">Место реализации:</div>
                                    <div class="product-primary-specs__value col-lg-6"><?= $location; ?></div>
                                </div>
                            <? } if (null !== ($location = $product->getLocation())) { ?>
                                <div class="product-primary-specs__row row">
                                    <div class="product-primary-specs__name col-lg-2">Место реализации:</div>
                                    <div class="product-primary-specs__value col-lg-6">
                                        <div><?= $location; ?></div>
                                        <? //TODO: у нас пока нет рассчёта расстояния
                                        /*
                                        <span class="small">Примерное расстояние 1450км<a href="#">Смотреть на карте</a></span>
                                        */
                                        ?>
                                    </div>
                                </div>
                            <? } ?>
                            <? if (count($product->getDeliveryTermsHelper()->getDeliveryTerms())) { ?>
                                <div class="product-primary-specs__row row">
                                    <div class="product-primary-specs__name col-lg-2">Условия:</div>
                                    <div class="product-primary-specs__value col-lg-6">
                                        <div><?= $product->getDeliveryTermsHelper()->getDeliveryTermsString(); ?></div>
                                        <? //TODO: у нас пока нет страницы на таблицу Incoterms
                                        /*
                                        <a class="small" href="#">Таблица Incoterms</a>
                                        */
                                        ?>
                                    </div>
                                </div>
                            <? } ?>
                            <? if  ($capacity = $product->getCapacityString()) { ?>
                                <div class="product-primary-specs__row row">
                                    <div class="product-primary-specs__name col-lg-2">Объёмы производства:</div>
                                    <div class="product-primary-specs__value product-primary-specs__value_capacity col-lg-6"><?= $capacity; ?></div>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col d-lg-none">
                    <? $type = 'small'; ?>
                    <?= Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/firm_profile_b2b.php'),[
                        'firm' => $firm,
                        'type' => $type,
                    ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6" data-type="affix-container">
                    <div class="block">
                        <div class="block__content">
                            <div class="tabs" data-type="affix-panel">
                                <? if (!empty($product->getText())) { ?>
                                    <a href="#about-product" class="tabs__item" data-type="affix-button">О товаре</a>
                                <? } ?>
                                <? if ($product->getVocabularyHelper()->getValues()) {?>
                                    <a href="#tech-specs" class="tabs__item" data-type="affix-button">Характеристики</a>
                                <? } ?>
                                <a href="#similar-products" class="tabs__item" data-type="affix-button">Похожие товары</a>
                            </div>
                        </div>
                    </div>
                    <? if (!empty($product->getText())) { ?>
                        <div class="block js-collapse" data-start-content-height="150">
                            <div class="block__title" data-type="affix-spy">
                                <h2 id="about-product" class="block__title-heading">Коротко о товаре</h2>
                            </div>
                            <div class="block__content js-collapse__content js-collapse__content_faded">
                                <?= $product->getText(); ?>
                            </div>
                            <a href="#" class="js-collapse__toggle">Показать все описание</a>
                        </div>
                    <? } ?>
                    <? if ($product->getVocabularyHelper()->getValues()) { ?>
                        <div class="block js-collapse" data-start-content-height="240">
                            <div class="block__title" data-type="affix-spy">
                                <h2 id="tech-specs" class="block__title-heading">Технические характиристики</h2>
                            </div>
                            <div class="block__content js-collapse__content js-collapse__content_faded">
                                <?= Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/vocabulary_terms.php'),[
                                    'product' => $product,
                                ]);
                                ?>
                            </div>
                            <a href="#" class="js-collapse__toggle">Показать все технические характеристики</a>
                        </div>
                    <? } ?>
                    <? // TODO: реализовать блок продвигаемых поставщиков (мобильная вёрстка)
                    /*
                    <div class="small-block small-block_normal-padding d-lg-none">
                        <div class="small-block__title">Реклама: Возможно вас заинтересуют</div>
                        <div class="small-block__content">
                            <div class="firms-slider">
                                <div class="firms-slider__slide">
                                    <div class="elements-list">
                                        <div class="elements-list__item">
                                            <div class="elements-list__item-image">
                                                <img src="/pages/b2b/img/company-01.jpg" alt="Логотип компании">
                                            </div>
                                            <div class="elements-list__item-content">
                                                <a href="#" class="elements-list__item-title">Ассоциация развития стального строительства</a>
                                                <div class="elements-list__item-row">
                                                    <div class="breadcrumbs">
                                                        <div class="breadcrumbs__item">
                                                            <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_construction"></a>
                                                            <div class="breadcrumbs__item-title"><a href="#">Строительство</a></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="firms-slider__slide">
                                    <div class="elements-list">
                                        <div class="elements-list__item">
                                            <div class="elements-list__item-image">
                                                <img src="/pages/b2b/img/company-02.jpg" alt="Логотип компании">
                                            </div>
                                            <div class="elements-list__item-content">
                                                <a href="#" class="elements-list__item-title">Granite Services International Kazakhstan</a>
                                                <div class="elements-list__item-row">
                                                    <div class="breadcrumbs">
                                                        <div class="breadcrumbs__item">
                                                            <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_energetics"></a>
                                                            <div class="breadcrumbs__item-title"><a href="#">Энергетика</a></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    */
                    ?>
                    <?
                    /* TODO реализовать вывод пары других товаров организации */
                    /*
                    <div class="block">
                        <div class="block__title">
                            <h2 class="block__title-heading">Другие товары компании</h2>
                        </div>
                        <div class="block__content">
                            <div class="elements-grid">
                                <div class="row">
                                    <div class="elements-grid__cell col-lg-4">
                                        <div class="row">
                                            <div class="col col_third col_no-right-gutter">
                                                <div class="elements-grid__cell-image">
                                                    <img src="/pages/b2b/img/wheat.jpg" alt="Фотография товара">
                                                </div>
                                            </div>
                                            <div class="col col_two-thirds">
                                                <div class="elements-grid__cell-content">
                                                    <a href="#" class="elements-grid__cell-title" title="Пшеница мягкая второй класс, влажность 60%, озимая">Пшеница мягкая второй класс, влажность 60%, озимая</a>
                                                    <div class="breadcrumbs">
                                                        <div class="breadcrumbs__item">
                                                            <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                        </div>
                                                        <div class="breadcrumbs__item">
                                                            <div class="breadcrumbs__item-title">
                                                                <a href="#">Пшеница</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="elements-grid__cell-bottom-container">
                                                        <div class="elements-grid__cell-vat">Цена без НДС</div>
                                                        <div class="elements-grid__cell-price-container">
                                                            <div class="elements-grid__cell-price">380 000 тг.</div>
                                                            <div class="elements-grid__cell-price-unit">за тонну</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="elements-grid__cell-footer-container">
                                            <div class="elements-grid__cell-bottom-container">
                                                <div class="elements-grid__cell-vat">Цена без НДС</div>
                                                <div class="elements-grid__cell-price-container">
                                                    <div class="elements-grid__cell-price">380 000 тг.</div>
                                                    <div class="elements-grid__cell-price-unit">за тонну</div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-footer">
                                                <button class="button button_small button_primary">
                                                    <span class="button__text">Коммерческий запрос</span>
                                                    <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_scales-plus"></span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="elements-grid__modal">
                                            <div class="row">
                                                <div class="col col_third col_no-right-gutter">
                                                    <div class="elements-grid__cell-image">
                                                        <img src="/pages/b2b/img/wheat.jpg" alt="Фотография товара">
                                                    </div>
                                                </div>
                                                <div class="col col_two-thirds">
                                                    <div class="elements-grid__cell-content">
                                                        <a href="#" class="elements-grid__cell-title" title="Пшеница мягкая второй класс, влажность 60%, озимая">Пшеница мягкая второй класс, влажность 60%, озимая</a>
                                                        <div class="breadcrumbs">
                                                            <div class="breadcrumbs__item">
                                                                <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                            </div>
                                                            <div class="breadcrumbs__item">
                                                                <div class="breadcrumbs__item-title">
                                                                    <a href="#">Пшеница</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="elements-grid__cell-bottom-container">
                                                            <div class="elements-grid__cell-vat">Цена без НДС</div>
                                                            <div class="elements-grid__cell-price-container">
                                                                <div class="elements-grid__cell-price">380 000 тг.</div>
                                                                <div class="elements-grid__cell-price-unit">за тонну</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-footer">
                                                <button class="button button_small button_primary">
                                                    <span class="button__text">Коммерческий запрос</span>
                                                    <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_scales-plus"></span>
                                                    <span class="button__text">К сравнению</span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                                                    <span class="button__text">Сохранить</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="elements-grid__cell col-lg-4">
                                        <div class="row">
                                            <div class="col col_third col_no-right-gutter">
                                                <div class="elements-grid__cell-image">
                                                    <img src="/pages/b2b/img/wheat.jpg" alt="Фотография товара">
                                                </div>
                                            </div>
                                            <div class="col col_two-thirds">
                                                <div class="elements-grid__cell-content">
                                                    <a href="#" class="elements-grid__cell-title" title="Пшеница мягкая второй класс, влажность 60%, озимая">Пшеница мягкая второй класс, влажность 60%, озимая</a>
                                                    <div class="breadcrumbs">
                                                        <div class="breadcrumbs__item">
                                                            <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                        </div>
                                                        <div class="breadcrumbs__item">
                                                            <div class="breadcrumbs__item-title">
                                                                <a href="#">Пшеница</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="elements-grid__cell-bottom-container">
                                                        <div class="elements-grid__cell-vat">Цена без НДС</div>
                                                        <div class="elements-grid__cell-price-container">
                                                            <div class="elements-grid__cell-price">380 000 тг.</div>
                                                            <div class="elements-grid__cell-price-unit">за тонну</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="elements-grid__cell-footer-container">
                                            <div class="elements-grid__cell-bottom-container">
                                                <div class="elements-grid__cell-vat">Цена без НДС</div>
                                                <div class="elements-grid__cell-price-container">
                                                    <div class="elements-grid__cell-price">380 000 тг.</div>
                                                    <div class="elements-grid__cell-price-unit">за тонну</div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-footer">
                                                <button class="button button_small button_primary">
                                                    <span class="button__text">Коммерческий запрос</span>
                                                    <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_scales-plus"></span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="elements-grid__modal">
                                            <div class="row">
                                                <div class="col col_third col_no-right-gutter">
                                                    <div class="elements-grid__cell-image">
                                                        <img src="/pages/b2b/img/wheat.jpg" alt="Фотография товара">
                                                    </div>
                                                </div>
                                                <div class="col col_two-thirds">
                                                    <div class="elements-grid__cell-content">
                                                        <a href="#" class="elements-grid__cell-title" title="Пшеница мягкая второй класс, влажность 60%, озимая">Пшеница мягкая второй класс, влажность 60%, озимая</a>
                                                        <div class="breadcrumbs">
                                                            <div class="breadcrumbs__item">
                                                                <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                            </div>
                                                            <div class="breadcrumbs__item">
                                                                <div class="breadcrumbs__item-title">
                                                                    <a href="#">Пшеница</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="elements-grid__cell-bottom-container">
                                                            <div class="elements-grid__cell-vat">Цена без НДС</div>
                                                            <div class="elements-grid__cell-price-container">
                                                                <div class="elements-grid__cell-price">380 000 тг.</div>
                                                                <div class="elements-grid__cell-price-unit">за тонну</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-footer">
                                                <button class="button button_small button_primary">
                                                    <span class="button__text">Коммерческий запрос</span>
                                                    <span class="button__icon button__icon_right button__icon_bill-send-white"></span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_scales-plus"></span>
                                                    <span class="button__text">К сравнению</span>
                                                </button>
                                                <button class="button button_small button_link">
                                                    <span class="button__icon button__icon_left button__icon_bookmark-plus"></span>
                                                    <span class="button__text">Сохранить</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    */
                    ?>
                    <?// похожие товары.?>
                    <?= SimilarProducts::widget(['product_id' => $product->id]); ?>
                </div>
                <div class="col-lg-2 d-sm-none d-lg-block">
                    <div data-type="float-element">
                        <?= Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/firm_profile_b2b.php'),[
                            'firm' => $firm,
                        ]);
                        ?>
                        <? // TODO: блок с рекламой
                        /*
                        <div class="small-block">
                            <div class="small-block__title">Реклама</div>
                            <div class="small-block__content">
                                <div class="placeholder-lg"></div>
                            </div>
                        </div>
                        */
                        ?>
                        <? // TODO: реализовать блок продвигаемых поставщиков
                        /*
                        <div class="small-block">
                            <div class="small-block__title">Возможно вас заинтересуют</div>
                            <div class="small-block__content">
                                <div class="elements-list">
                                    <div class="elements-list__item">
                                        <div class="elements-list__item-image">
                                            <img src="/pages/b2b/img/company-01.jpg" alt="Логотип компании">
                                        </div>
                                        <div class="elements-list__item-content">
                                            <a href="#" class="elements-list__item-title">Ассоциация развития стального строительства</a>
                                            <div class="elements-list__item-row">
                                                <div class="breadcrumbs">
                                                    <div class="breadcrumbs__item">
                                                        <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_construction"></a>
                                                        <div class="breadcrumbs__item-title"><a href="#">Строительство</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="elements-list__item">
                                        <div class="elements-list__item-image">
                                            <img src="/pages/b2b/img/company-02.jpg" alt="Логотип компании">
                                        </div>
                                        <div class="elements-list__item-content">
                                            <a href="#" class="elements-list__item-title">Granite Services International Kazakhstan</a>
                                            <div class="elements-list__item-row">
                                                <div class="breadcrumbs">
                                                    <div class="breadcrumbs__item">
                                                        <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_energetics"></a>
                                                        <div class="breadcrumbs__item-title"><a href="#">Энергетика</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="elements-list__item">
                                        <a href="#" class="elements-list__small-link">Как сюда попасть?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        */
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?// модальное окно с коммерческим запросом.?>
<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL, 'version' => CommercialRequest::MODAL_NEW]); ?>