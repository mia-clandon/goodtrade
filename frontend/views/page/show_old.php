<?
/**
 * @var \common\models\Page $page
 * @var \common\models\Page[] $pages
 */
?>

<div class="info-pages">
    <div class="info-pages-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-6">
                    <div class="sub-title"><?= $page->title; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="info-pages-content">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-md-2">
                    <div class="info-pages-nav">
                        <ul>
                            <? foreach ($pages as $item) { ?>
                            <li><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => $item->alias]); ?>"><?= $item->title; ?></a></li>
                            <? } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-6 col-md-4">
                    <div class="info-pages-text">
                        <? /*
                        <h3><?= $page->title; ?></h3>
                        */ ?>
                        <?= $page->text; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>