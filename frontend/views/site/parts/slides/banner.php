<?
/**
 * @var \common\models\MainSlider $slide
 * @var bool $show_col
 */

use common\modules\image\helpers\Image as ImageHelper;

$image = ImageHelper::i()->generateRelativeImageLink($slide->image, 300, 170);

?>

<div class="cards-list__card <?= ($slide->getTypeNum() == 1 || $slide->getTypeNum() == 3) && $show_col ?'col-4':'col-8' ?>
                             <?= $slide->getTypeNum() == 1?'cards-list__card_borderless':'' ?>
                             <?= $slide->getTypeNum() == 2?'cards-list__card_accent-yellow-wide':'' ?>
                             <?= $slide->getTypeNum() == 3 || $slide->getTypeNum() == 4?'cards-list__card_tall':'' ?> "
                             style = "<?=!$show_col?'margin-bottom: 15px':''?>">
    <div class="cards-list__card-content">
        <div class="cards-list__card-bg">
            <img src="<?= $image; ?>" alt="<?= $slide->title; ?>" >
        </div>
        <div class="row">
            <div class="col-8">
                <h2 class="cards-list__card-heading"><?= $slide->title; ?>!</h2>
            </div>
        </div>
        <? if($slide->getTypeNum() == 2) { ?>
            <div class="cards-list__card-content-bottom-row row">
                <div class="col-4">
                    <p class="cards-list__card-text"><?=  mb_substr(strip_tags($slide->description), 0, 150); ?></p>
                </div>
                <div class="col-4">
                    <a href="<?= $slide->link; ?>" class="button button_full button_primary">
                        <span class="button__text"><?= $slide->button; ?></span>
                    </a>
                </div>
            </div>
        <? } else { ?>
            <div class="cards-list__card-content-bottom-row row">
                <div class="col-8">
                    <p class="cards-list__card-text"><?=  mb_substr(strip_tags($slide->description), 0, 100); ?></p>
                </div>
            </div>
            <div class="cards-list__card-content-bottom-row row">
                <div class="col-8">
                    <a href="<?= $slide->link; ?>" class="button button_full button_primary">
                        <span class="button__text"><?= $slide->button; ?></span>
                    </a>
                </div>
            </div>
        <? } ?>
    </div>
</div>