<?php
/**
 * @var \common\models\Category $category
 * @var \common\models\Category[] $children
 * @var \common\models\Category[] $siblings
 * @var \common\models\goods\Product[] $products
 * @var \common\models\CategorySlider[] $slides
 * @var \common\models\firms\Firm[] $firms
 * @var int $product_count
 * @var int $product_limit
 * @var int $firm_count
 * @var int $firm_limit
 * @author yerganat
 */

use frontend\components\widgets\CommercialRequest;

$condition = '';
?>
<main>
    <div class="container container_main">
        <div class="row">
            <div class="col-lg-6">
                <?
                if ($slides) {
                    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/category/parts/slider.php'), [
                        'slides' => $slides,
                    ]);
                }
                ?>

                <? if ($category->small_text) { ?>
                <div class="block">
                    <div class="block__title">
                        <h2 class="block__title-heading"><?= $category->title ?> в Казахстане</h2>
                    </div>
                    <!-- Слайдер подкатегорий для мобильной вёрстки -->
                    <div class="small-block d-lg-none">
                        <div class="small-block__title">Каталог</div>
                        <div class="small-block__content">
                            <?=
                            Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/category/parts/subcat-slider.php'), [
                                'category' => $category,
                                'children' => $children,
                                'siblings' => $siblings,
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="block__content">
                        <?
                        $small_text = trim($category->small_text);
                        $more_link_text = '<a href="#category-description" class="block__more-link-inline">Читать подробней ↓</a>';

                        if (mb_substr($small_text, -4) === "</p>") {
                            $small_text = mb_substr($small_text, 0, -4) . $more_link_text . mb_substr($small_text, -4);
                        }
                        ?>
                        <?=$small_text?>
                        <a href="#category-description" class="block__more-link-block">Читать подробней ↓</a>
                    </div>
                </div>
                <? } ?>
                <div class="block">
                    <div class="block__title">
                        <h2 class="block__title-heading">Популярные товары в категории <?= $category->title ?></h2>
                    </div>
                    <div class="block__content">
                        <div class="elements-grid">
                            <div class="row products_content">
                                <?
                                if ($products) {
                                    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/new_product_list.php'), [
                                        'product_list' => $products,
                                        'category' => $category,
                                    ]);
                                }
                                ?>
                            </div>
                            <? if (count($products) < $product_count) { ?>
                            <div class="row more_product_area">
                                <div class="col col-lg col-lg_two-thirds m-auto">
                                    <button class="elements-grid__button button button_full more_product_button"  data-category="<?=$category->id?>"
                                            data-offset="<?=$product_limit?>" data-limit="<?=$product_limit?>" data-count="<?=$product_count?>">
                                        <span class="button__text">Показать еще</span>
                                    </button>
                                </div>
                            </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div class="block__title">
                        <h2 class="block__title-heading">Популярные поставщики в категории <?= $category->title ?></h2>
                    </div>
                    <div class="block__content">
                        <div class="elements-grid">
                            <div class="row firms_content">
                                <?
                                if ($firms) {
                                    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/category/parts/firms.php'), [
                                        'firms' => $firms,
                                        'category' => $category,
                                    ]);
                                }
                                ?>
                            </div>
                            <?if (count($firms) < $firm_count) {?>
                            <div class="row more_firm_area">
                                <div class="col col-lg col-lg_two-thirds m-auto">
                                    <button class="elements-grid__button button button_full more_firm_button" data-category="<?=$category->id?>"
                                            data-offset="<?=$firm_limit?>" data-limit="<?=$firm_limit?>" data-count="<?=$firm_count?>">
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
                            <div class="col-lg-4">
                                <div class="placeholder-md"></div>
                            </div>
                            <div class="col-lg-4">
                                <div class="placeholder-md"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div id="category-description" class="block__title">
                        <h2 class="block__title-heading"><?= $category->title ?></h2>
                    </div>
                    <div class="block__content">
                        <?=$category->text?>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 d-none d-lg-block">
                <?=
                Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/category/parts/nav.php'), [
                    'category' => $category,
                    'children' => $children,
                    'siblings' => $siblings,
                ]);
                ?>
            </div>
        </div>
    </div>
</main>

<?
/* TODO реализовать логику новостей */
/* Не знаю как этот скрипт подключить на страницу, как это сделано с main-slider.js
<script type="text/javascript" src="/frontend/app/pages/b2b/news-slider.js"></script>
<script type="text/javascript" src="/js/for-static-pages/b2b/news-slider.js"></script>
*/
/*
<div class="news-block">
    <div class="container container_main">
        <div class="block block_no-padding">
            <div class="block__title">
                <div class="block__title-left">
                    <h2 class="block__title-heading">Новости</h2>
                    <a href="#" class="block__title-link">Смотреть все новости</a>
                </div>
                <div class="block__title-right">
                    <div class="block__title-text">Подписаться на еженедельную рассылку</div>
                    <button class="button button_primary">
                        <span class="button__text">Подписаться</span>
                        <span class="button__icon button__icon_right button__icon_play-white"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="news-slider-block">
        <div class="news-slider-block__arrow news-slider-block__arrow_left"></div>
        <div class="news-slider-block__arrow news-slider-block__arrow_right"></div>
        <div class="news-slider-block__container container container_main">
            if (!is_null($condition)) {
                echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/news.php'), [
                    'condition' => $condition,
                ]);
            }
        </div>
    </div>
</div>
*/
?>

<?// модальное окно с коммерческим запросом.?>
<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL, 'version' => CommercialRequest::MODAL_NEW]); ?>