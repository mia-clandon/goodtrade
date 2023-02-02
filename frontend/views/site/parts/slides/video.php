<?
/**
 * @var \common\models\MainSlider $slide
 */

use common\modules\image\helpers\Image as ImageHelper;

$image = ImageHelper::i()->generateRelativeImageLink($slide->image, 300, 170);

?>

<div class="cards-list__card cards-list__card_video col-4">
    <div class="cards-list__card-content">
        <div class="cards-list__card-bg">
            <img src="<?= $image ?>" alt="<?= $slide->title; ?>">
        </div>
        <div class="cards-list__card-type">
            <div class="label label_primary-invert"><?= $slide->getCurrentTagText(); ?></div>
        </div>
        <div class="cards-list__card-content-bottom">
            <p class="cards-list__card-title"><?= $slide->title?></p>
        </div>
        <div class="cards-list__card-more cards-list__card-more_small">
            <div class="cards-list__card-video-link">
                <a href="#"><span class="cards-list__card-video-icon cards-list__card-video-icon_small"></span>Смотреть видео</a>
            </div>
        </div>
    </div>
</div>