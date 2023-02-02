<?
/**
 * @var string $filter_form
 * @var \common\models\goods\Product[] $product_list
 * @var array $sort_links
 * @var string $clear_filter_sort_form
 * @var \common\libs\SearchHelper $search_helper
 * @var int $found_count
 * @var null|\common\models\Category $category
 * @var bool $need_show_load_button
 */

use frontend\components\widgets\CommercialRequest;

?>
<div class="container container_main">
    <div class="row">
        <div class="col-lg-6">
            <? // todo: реализовать вывод переключателя с количеством товаров и компаний ?>
            <? /*
            <div class="small-block d-lg-none">
                <div class="small-block__content">
                    <div class="tumbler tumbler_full">
                        <div class="tumbler__buttons">
                            <a href="#" class="tumbler__button tumbler__button_active">214 товаров</a>
                            <a href="#" class="tumbler__button">52 компании</a>
                        </div>
                    </div>
                </div>
            </div>
            */ ?>
            <? // todo: реализовать вывод слайдера категорий, как на frontend/views/category/show.php и frontend/views/category/parts/buttons-slider.php ?>
            <? /*
            <div class="small-block d-lg-none">
                <div class="small-block__title">Уточните категорию</div>
                <div class="small-block__content">
                    <div class="buttons-slider-block">
                        <div class="buttons-slider">
                            <a href="/category/2" class="button button_normalcase"><span class="button__text">Сельское, лесное и рыбное хозяйство</span></a>
                            <a href="/category/8" class="button button_normalcase"><span class="button__text">Производство продуктов питания</span></a>
                            <a href="/category/18" class="button button_normalcase"><span class="button__text">Производство табачных изделий</span></a>
                            <a href="/category/56" class="button button_normalcase"><span class="button__text">Тестовая категория</span></a>
                        </div>
                    </div>
                </div>
            </div>
            */ ?>
            <? // Блок сортировки для мобильной вёрстки ?>
            <? if ($found_count > 0) { ?>
            <div class="small-block d-lg-none">
                <div class="small-block__title">Сортировка</div>
                <div class="small-block__content">
                    <div class="sorting">
                        <? foreach ($sort_links as $sort_name => $data) { ?>
                            <a href="<?= $data['url'];?>" class="sorting__param <?if ($data['is_active']){?>sorting__param_<?=$data['current_direction']?><?}?>"><?= $sort_name; ?></a>
                        <? } ?>
                    </div>
                </div>
            </div>
            <? } ?>
            <div class="block">
                <? // Блок сортировки для полной вёрстки ?>
                <? if ($found_count > 0) { ?>
                <div class="block__title d-none d-lg-block">
                    <div class="sorting sorting_shifted-down">
                        <div class="sorting__title">Сортировать:</div>
                        <? foreach ($sort_links as $sort_name => $data) { ?>
                            <a href="<?= $data['url'];?>" class="sorting__param <?if ($data['is_active']){?>sorting__param_<?=$data['current_direction']?><?}?>"><?= $sort_name; ?></a>
                        <? } ?>
                    </div>
                </div>
                <? } ?>

                <div class="block__content">
                    <div class="elements-grid">
                        <div class="row product-list-wrapper">
                            <?
                                echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/new_product_list.php'),[
                                    'product_list' => $product_list ?? [],
                                ]);
                            ?>
                        </div>
                        <input name="product_found_page" value="0" type="hidden" />
                        <? if ($need_show_load_button) { ?>
                        <div class="row">
                            <div class="col col-lg col-lg_two-thirds m-auto">
                                <button class="product-load-more elements-grid__button button button_full">
                                    <span class="button__text">Показать еще</span>
                                </button>
                            </div>
                        </div>
                        <? } ?>
                    </div>
                </div>
            </div>
            <div class="block d-none d-lg-block">
                <div class="block__content">
                    <div class="row">
                        <div class="col-lg">
                            <div class="placeholder-md"></div>
                        </div>
                        <div class="col-lg">
                            <div class="placeholder-md"></div>
                        </div>
                    </div>
                </div>
            </div>

            <? // todo: реализовать вывод списка поставщиков при поиске по товарам ?>
            <? /*
            <div class="block">
                <div class="block__title">
                    <h2 class="block__title-heading">Попробуйте поискать среди поставщиков</h2>
                </div>
                <div class="block__content">
                    <div class="elements-grid">
                        <div class="row">
                            <div class="elements-grid__cell col-lg-4">
                                <div class="row">
                                    <div class="col col_third col_no-right-gutter">
                                        <div class="elements-grid__cell-image">
                                            <img src="/pages/b2b/img/olam.jpg" alt="Логотип поставщика">
                                        </div>
                                    </div>
                                    <div class="col col_two-thirds">
                                        <div class="elements-grid__cell-content">
                                            <a href="#" class="elements-grid__cell-title"
                                               title="ТОО «Агро Компания Olam»">ТОО «Агро Компания Olam»</a>
                                            <div class="breadcrumbs">
                                                <div class="breadcrumbs__item">
                                                    <a href="#"
                                                       class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                </div>
                                                <div class="breadcrumbs__item">
                                                    <div class="breadcrumbs__item-title">
                                                        <a href="#">Растениеводство</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-thumbs">
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-01.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-02.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-03.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-04.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="elements-grid__cell-footer-container">
                                    <div class="elements-grid__cell-thumbs">
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-01.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-02.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-03.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-04.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="elements-grid__cell col-lg-4">
                                <div class="row">
                                    <div class="col col_third col_no-right-gutter">
                                        <div class="elements-grid__cell-image">
                                            <img src="/img/product_category_stubs/agriculture.png"
                                                 alt="Логотип поставщика">
                                        </div>
                                    </div>
                                    <div class="col col_two-thirds">
                                        <div class="elements-grid__cell-content">
                                            <a href="#" class="elements-grid__cell-title"
                                               title="Животноводческая компания ООО «Интеркрос Центр»">Животноводческая
                                                компания ООО «Интеркрос Центр»</a>
                                            <div class="breadcrumbs">
                                                <div class="breadcrumbs__item">
                                                    <a href="#"
                                                       class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                </div>
                                                <div class="breadcrumbs__item">
                                                    <div class="breadcrumbs__item-title">
                                                        <a href="#">Животноводство</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-thumbs">
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-05.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-06.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-07.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="elements-grid__cell-footer-container">
                                    <div class="elements-grid__cell-thumbs">
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-05.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-06.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-07.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="elements-grid__cell col-lg-4">
                                <div class="row">
                                    <div class="col col_third col_no-right-gutter">
                                        <div class="elements-grid__cell-image">
                                            <img src="/pages/b2b/img/olam.jpg" alt="Логотип поставщика">
                                        </div>
                                    </div>
                                    <div class="col col_two-thirds">
                                        <div class="elements-grid__cell-content">
                                            <a href="#" class="elements-grid__cell-title"
                                               title="ТОО «Агро Компания Olam»">ТОО «Агро Компания Olam»</a>
                                            <div class="breadcrumbs">
                                                <div class="breadcrumbs__item">
                                                    <a href="#"
                                                       class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                                                </div>
                                                <div class="breadcrumbs__item">
                                                    <div class="breadcrumbs__item-title">
                                                        <a href="#">Растениеводство</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="elements-grid__cell-thumbs">
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-01.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-02.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-03.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                                <a href="#" class="elements-grid__cell-thumb">
                                                    <img src="/pages/b2b/img/provider-goods-04.jpg"
                                                         alt="Фото товара компании">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="elements-grid__cell-footer-container">
                                    <div class="elements-grid__cell-thumbs">
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-01.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-02.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-03.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                        <a href="#" class="elements-grid__cell-thumb">
                                            <img src="/pages/b2b/img/provider-goods-04.jpg"
                                                 alt="Фото товара компании">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="elements-grid__cell col-lg-4">
                                <a href="#" class="elements-grid__cell-placeholder">Показать остальные 49 компаний</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            */ ?>

        </div>
        <div class="col-lg-2 d-none d-lg-block">
            <div class="inner-bar">

                <?/*//todo:
                <nav>
                    <div class="list-title">Уточните категорию</div>
                    <div class="breadcrumbs">
                        <div class="breadcrumbs__item">
                            <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></a>
                            <div class="breadcrumbs__item-title">
                                <a href="#">Сельское хозяйство</a>
                            </div>
                        </div>
                        <div class="breadcrumbs__item"></div>
                    </div>
                    <ul class="linear-list">
                        <li>
                            <a href="#">Растениеводство</a>&nbsp;&nbsp;(85)
                            <ul>
                                <li><a href="#">Пшеница</a>&nbsp;&nbsp;(15)</li>
                                <li><a href="#">Овес</a>&nbsp;&nbsp;(3)</li>
                                <li><a href="#">Подсолнечник</a>&nbsp;&nbsp;(17)</li>
                                <li class="list-more-link"><a href="#">Еще</a>&nbsp;&nbsp;(46)</li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Животноводство</a>&nbsp;&nbsp;(15)
                            <ul>
                                <li><a href="#">Скотоводство</a>&nbsp;&nbsp;(3)</li>
                                <li><a href="#">Свиноводство</a>&nbsp;&nbsp;(7)</li>
                                <li><a href="#">Коневодство</a>&nbsp;&nbsp;(1)</li>
                                <li class="list-more-link"><a href="#">Еще</a>&nbsp;&nbsp;(4)</li>
                            </ul>
                        </li>
                        <li class="list-more-link list-more-link_secondary"><a href="#">Показать еще 3 категории</a>
                        </li>
                    </ul>
                </nav>
                */ ?>

                <?= $filter_form; ?>

            </div>
        </div>
    </div>

    <? if (null !== $category) { ?>
    <div class="row d-none d-lg-block">
        <div class="col-lg-8">
            <div class="block">
                <div class="block__title">
                    <h2 class="block__title-heading"><?= $category->title; ?></h2>
                </div>
                <div class="block__content">
                    <?= $category->text; ?>
                </div>
            </div>
        </div>
    </div>
    <? } ?>
</div>


<?// модальное окно с коммерческим запросом.?>
<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL, 'version' => CommercialRequest::MODAL_NEW]); ?>

<?// модальное окно с формой фильтра поиска для мобильной вёрстки.?>
<div class="modal is-hidden" data-type="filter-form">
    <div class="icon icon_delete" data-action="filter-form-close"></div>
    <div class="container container_main">
        <div class="row">
            <div class="col">
                <div class="modal__heading">Фильтр</div>
                <?= $filter_form; ?>
            </div>
        </div>
    </div>
</div>
