<?
/**
 * Вывод избарнную компанию
 * @var \common\models\firms\Firm $firm
 * @var \common\models\goods\Product[] $products
 * @var \common\models\Chrono[] $chronos
 * @var int $firm_id
 */

use common\modules\image\helpers\Image as ImageHelper;
use yii\helpers\Url;

$first_activity = $firm->getCategories()->one();

$image = null;
if ($firm->image) {
    $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 140, 140, ImageHelper::RESIZE_MODE_CROP);
}

if ($first_activity && null === $image) {
    $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
}

$session = \Yii::$app->session;
?>

<div class="row">

    <div class="favorites-company-description">
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <div class="row">
                <div class="favorites-company-description__image">
                    <img src="<?= $image ?>" alt="Логотип компании">
                </div>
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <h2 class="favorites-company-description__title"><?= $firm->getTitle() ?></h2>
            <? if ($firm->isTopSeller()) { ?>
                <div class="top-rated">Топовый продавец</div>
            <? } ?>
            <div class="favorites-company-description__address">
                <p><?= $firm->getLocation() ?></p>
                <!--<small>Расстояние 1450км <a href="#">Смотреть на карте</a></small>-->
            </div>
        </div>
    </div>

    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        <div class="row">
            <div class="favorites-note">
                <div class="favorites-note__inner-container">
                    <div class="favorites-note__title">Заметки</div>
                    <textarea class="favorites-note__text js-favorite-note" name="favorites-note__text" placeholder="Запишите сюда заметки, чтобы не забыть" data-firm-id="<?=$firm_id?>"><?= $session->get('note'.$firm_id); ?></textarea>
                    <div class="favorites-note__description">Ваши заметки видны только Вам</div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <div class="favorites-tabs">
            <ul class="tabs-header">
                <li class="tabs-btn is-active"><a href="<?=Url::to(['favorite/index', 'firm_id' => $firm_id])?>">Основное</a></li>
                <? if(count($products)>0) { ?>
                <li class="tabs-btn js-show-products" data-firm-id="<?=$firm_id?>"><a href="#">Показать все отложенные товары этой компании <span><?=count($products)?></span></a></li>
                <? } ?>
                <li class="tabs-btn js-show-chronos"  data-firm-id="<?=$firm_id?>"><a href="#">Хронология</a></li>
                <!--<li class="tabs-btn"><a href="#">Документы</a></li>-->
            </ul>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 js-favorite-content">

        <?
        if($chronos) {
            echo \Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/parts/events.php'), [
                'firm' => $firm,
                'chronos' => $chronos,
            ]);
        }
        ?>

        <?=
        \Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/parts/products.php'), [
            'products' => $products,
            'firm_id' => $firm_id
        ]);
        ?>


        <?
        //\Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/parts/documents.php'), []);
        ?>

    </div>
</div>