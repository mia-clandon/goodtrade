<?
/**
 * Блок с профилем организации.
 * @var \common\models\firms\Firm $firm
 * @var \common\models\Location $city
 * @author Артём Широких kowapssupport@gmail.com
 */

use yii\helpers\Html;
use common\modules\image\helpers\Image as ImageHelper;

// todo: подобные конструкции можно вынести в helper, к примеру FirmHelper.php, или декоратор FirmProfileDecorator.php
$image = null;
$city = null;
if ($firm) {
    $city = $firm->getCity()->one();

    if ($firm->image) {
        $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 140, 140, ImageHelper::RESIZE_MODE_CROP);
    }
    else {
        /** @var \common\models\Category|null $first_activity */
        $first_activity = $firm->getCategories()->one();
        if ($first_activity) {
            $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
        }
    }
}

if ($firm) { ?>
    <div class="profile-logo">
    <? if ($image) {?>
        <?= Html::img('/img/placeholders/140x140.png', [
            'class' => 'lazy',
            'data-original' => $image,
        ]);?>
        <noscript>
            <img src="<?= $image; ?>" alt="Логотип компании">
        </noscript>
    <? } ?>
    </div>
    <div class="profile-title">
        <a href="<?= Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]);?>" title="<?= $firm->getTitle(); ?>"><?= $firm->getTitle(); ?></a>
    </div>
    <? if ($city) { ?>
    <div class="profile-location">
        <?= $city->title; ?>
    </div>
    <?  } ?>
    <? if ($firm->isTopSeller()) { ?>
    <div class="profile-snippets">
        <div class="top-rated">Топовый продавец</div>
    </div>
    <? } ?>

    <? /*
    <div class="btn btn-sm btn-lowercase profile-btn">Добавить в список</div>
    */ ?>
<? } ?>