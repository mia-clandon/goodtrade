<?
/**
 * @var MainSlider[] $slides
 * @var array $array_by_slide
 * @var array $activity_array
 */

use common\models\MainSlider;
use frontend\components\widgets\CommercialRequest;

if (null !== $slides) {
    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/slide.php'), ['slides'=>$slides, 'array_by_slide'=>$array_by_slide,]);
}
?>
    <main>
        <div class="container container_main content_info">
            <div class="row">
                <div class="col">
                    <div class="block">
                        <div class="block__title">
                            <span>
                                <img src="../img/icons_new/landing/features/first.svg" alt="">
                            </span>
                        </div>
                        <div class="block__content">
                            <span>Торговая площадка,</span>
                            <span>объединяющая производителей,</span>
                            <span>покупателей и продавцов</span>
                        </div>
                    </div>
                </div>
                <div class="col col_desktop item_features_two">
                    <div class="block">
                        <div class="block__title">
                            <img src="../img/icons_new/landing/features/main.svg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col item_features_three">
                    <div class="block">
                        <div class="block__title">
                            <span>
                                <img src="../img/icons_new/landing/features/second.svg" alt="">
                            </span>
                        </div>
                        <div class="block__content">
                            <span>Объединение всех видов </span>
                            <span>логистики в одну удобную систему</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="block">
                        <div class="block__title">
                            <span>
                                <img src="../img/icons_new/landing/features/third.svg" alt="">
                            </span>
                        </div>
                        <div class="block__content">
                            <span>Прямой доступ к финансовым инструментам </span>
                            <span>банков и страховых компаний </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="features features_top">
            <div class="item_feature item_feature_one">
                <div class="item_feature_header">
                    <h1 class="header_feature">Интеллектуальный </br> поиск</h1>
                    <span>
                        <img src="../img/icons_new/landing/features/head.svg" alt="">
                    </span>
                </div>
                <p class="item_feature_medium">
                    Введите запрос на <span class="item_feature_main_info">интеллектуальный</span> </br>
                    <span class="item_feature_main_info">поиск</span> в поле
                    ввода
                    на навигационной панели.
                </p>
                <img src="../img/icons_new/landing/features/important.svg" alt="">
                <p class="item_feature_footer">
                    При поиске система <span class="item_feature_main_info">автоматически</span> отбросит <br>
                    слова,
                    которые не содержатся в базе данных.
                </p>
            </div>
            <div class="item_feature item_feature_two">
                <div class="item_feature_header">
                    <h1 class="header_feature">Доступная </br> аналитика</h1>
                    <span>
                    <img src="../img/icons_new/landing/features/eye.svg" alt="">
                </span>
                </div>
                <p class="item_feature_medium">
                    Воспользуйтесь <span class="item_feature_main_info">
                        системой сравнения</span> </br> коммерческих предложений.
                </p>
                <img src="../img/icons_new/landing/features/important.svg" alt="">
                <p class="item_feature_footer">
                    Добавляйте интересные <br> предложения в <span class="item_feature_main_info">избранное</span> и
                    принимайте <br> решения для
                    <span class="item_feature_main_info">заключения сделок</span>.
                </p>
            </div>
        </section>
        <section class="features features_bottom">
            <div class="item_feature item_feature_one">
                <div class="item_feature_header">
                    <h1 class="header_feature">Упрощенные </br> коммуникации</h1>
                    <img src="../img/icons_new/landing/features/communication_icon.svg" alt="">
                </div>
                <p class="item_feature_medium">
                    Воспользуйтесь нашей <span class="item_feature_main_info">автоматизированной </br>
                        системой обмена </span> коммерческими предложениями.
                </p>
                <img src="../img/icons_new/landing/features/important.svg" alt="">
                <p class="item_feature_footer">
                    Управляйте коммуникациями по <span class="item_feature_main_info">календарю</span> и </br> выводите
                    на
                    <span class="item_feature_main_info">заключение сделок</span>.
                </p>
            </div>
        </section>
        <section class="options">
            <div class="content">
                <div class="item_options">
                    <img src="../img/icons_new/landing/features/people.svg" alt="">
                    <p>
                        Развитие новых </br> торговых связей
                    </p>
                </div>
                <div class="item_options">
                    <img src="../img/icons_new/landing/features/process-icon.svg" alt="">
                    <p>
                        Эффективный рост </br> бизнеса
                    </p>
                </div>
                <div class="item_options">
                    <img src="../img/icons_new/landing/features/icon_progress.svg" alt="">
                    <p>
                        Создание новых </br> бизнес-идей
                    </p>
                </div>
            </div>
        </section>
        <section class="step_by_step">
            <div class="content">
                <div class="item_steps step_one" data-type="modal-open" data-modal="overlay-sign-up">
                    <img src="../img/icons_new/landing/steps/step_1.svg" alt="" class="item_step_desktop">
                    <img src="../img/icons_new/landing/steps/step_1_adaptive.svg" alt=""
                         class="item_step_adaptive_tablet">
                    <img src="../img/icons_new/landing/steps/step_1_mobile.svg" alt="" class="item_step_mobile">
                    <img width="25px" height="23px" src="../img/icons_new/landing/steps/down_arrow.svg" alt=""
                         class="item_step_adaptive down_arrow">
                </div>
                <div class="item_steps step_two">
                    <img src="../img/icons_new/landing/steps/step_2.svg" alt="" class="item_step_desktop">
                    <img src="../img/icons_new/landing/steps/step_2_adaptive.svg" alt="" class="item_step_adaptive">
                    <img width="25px" height="23px" src="../img/icons_new/landing/steps/down_arrow.svg" alt=""
                         class="item_step_adaptive down_arrow">
                </div>
                <div class="item_steps step_three">
                    <img src="../img/icons_new/landing/steps/step_3.svg" alt="" class="item_step_desktop">
                    <img src="../img/icons_new/landing/steps/step_3_adaptive.svg" alt="" class="item_step_adaptive">
                    <img width="25px" height="23px" src="../img/icons_new/landing/steps/down_arrow.svg" alt=""
                         class="item_step_adaptive down_arrow">
                </div>
                <div class="item_steps step_four">
                    <img src="../img/icons_new/landing/steps/step_4.svg" alt="" class="item_step_desktop">
                    <img src="../img/icons_new/landing/steps/step_4_adaptive.svg" alt=""
                         class="item_step_adaptive_tablet">
                    <img src="../img/icons_new/landing/steps/step_4_mobile.svg" alt="" class="item_step_mobile">
                </div>
        </section>
    </main>

<? // if (null !== $activity_array) { ?>
    <!--    --><? // /* Не знаю как этот скрипт подключить на страницу, как это сделано с main-slider.js
//<script type="text/javascript" src="/frontend/app/pages/b2b/categories-slider.js"></script>
//*/ ?>
    <!--    <script type="text/javascript" src="/js/for-static-pages/b2b/categories-slider.js"></script>-->
    <!--    <div class="categories-block">-->
    <!--        <div class="container container_main">-->
    <!--            <div class="block block_no-padding">-->
    <!--                <div class="block__title">-->
    <!--                    <h2 class="block__title-heading">Сферы деятельности</h2>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="categories-slider-block">-->
    <!--            <div class="categories-slider-block__container container container_main">-->
    <!--                <div class="block">-->
    <!--                    <div class="block__content">-->
    <!--                        <div class="categories-slider row">-->
    <!--                            --><? //
//                            echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/activities.php'), ['activity_array'=>$activity_array,]);
//                            ?>
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
<? // } ?>

<?
/* TODO реализовать логику новостей */
/* Не знаю как этот скрипт подключить на страницу, как это сделано с main-slider.js
<script type="text/javascript" src="/frontend/app/pages/b2b/news-slider.js"></script>
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

<?
/* TODO реализовать логику видео слайдера */
/*
 * if (!is_null($condition)) {
    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/video.php'), [
        'condition' => $condition,
    ]);
}
*/
?>

<? // модальное окно с коммерческим запросом.?>
<?= CommercialRequest::widget(['type'=>CommercialRequest::REQUEST_TYPE_MODAL, 'version'=>CommercialRequest::MODAL_NEW]); ?>