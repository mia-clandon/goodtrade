<?
/**
 * @var \common\models\Page $page
 * @var \common\models\Page[] $pages
 */
?>

<main>
    <div class="container container_main">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                <nav class="page-bar">
                    <ul class="linear-list">
                        <? foreach ($pages as $item) { ?>
                            <li>
                                <a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => $item->alias]); ?>"><?= $item->title; ?></a>
                            </li>
                        <? } ?>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-6">
                <div class="block">
                    <div class="block__title">
                        <h2 class="block__title-heading"><?= $page->title; ?></h2>
                    </div>
                    <!-- Слайдер страниц для мобильной вёрстки -->
                    <div class="small-block d-lg-none">
                        <div class="small-block__content">
                            <div class="buttons-slider-block">
                                <div class="buttons-slider">
                                    <? foreach ($pages as $item) { ?>
                                        <a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => $item->alias]); ?>" class="button button_normalcase"><span class="button__text"><?= $item->title; ?></span></a>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block__content">
                        <?= $page->text; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>