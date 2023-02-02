<?
/**
 * Блок слайдера
 * @var \common\models\CategorySlider[] $slides
 * @author yerganat
 */

use common\modules\image\helpers\Image as ImageHelper;
?>
<div class="info-slider">
    <div class="info-slider__slides-container">
        <? foreach ($slides as $key => $slide) { ?>
        <div class="info-slider__slide">
            <div class="info-slider__slide-image">
                <? if($slide->image) {
                    $image = ImageHelper::i()->generateRelativeImageLink($slide->image, 950, null);
                    ?>
                    <img src="<?=$image?>" alt="Изображение слайда">

                <? } ?>
            </div>
            <div class="info-slider__slide-content">
                <div class="info-slider__slide-note-container">
                    <div class="label label_primary-invert"><?=$slide->getCurrentTagText()?></div>
                    <? if ($slide->link) { ?>
                        <div class="info-slider__slide-note"><span>Источник: </span><?=$slide->link?></div>
                    <? } ?>
                </div>
                <div class="info-slider__slide-heading"><?=$slide->title?></div>
<!--
                <div class="info-slider__slide-info">
                    <div class="info-slider__slide-info-date">Сегодня, 14:21</div>
                    <div class="info-slider__slide-info-views">247</div>
                    <div class="info-slider__slide-info-comments">4</div>
                </div>
                <div class="info-slider__slide-more">
                    <a href="#" class="info-slider__slide-more-link">Читать далее</a>
                </div>
-->
            </div>
            <!-- Панель слайдера для мобильной вёрстки -->
            <div class="info-slider__panel">
                <div class="info-slider__panel-item">
                    <div class="info-slider__panel-item-number">
                        <div class="info-slider__panel-item-circle-wrap info-slider__panel-item-circle-wrap_left">
                            <div class="info-slider__panel-item-circle info-slider__panel-item-circle_left"></div>
                        </div>
                        <div class="info-slider__panel-item-circle-wrap info-slider__panel-item-circle-wrap_right">
                            <div class="info-slider__panel-item-circle info-slider__panel-item-circle_right"></div>
                        </div>
                        <?=++$key;?>
                    </div>
                    <div class="info-slider__panel-item-title">
                        <?=$slide->getCurrentTagText()?>: <?=mb_substr($slide->title, 0, 130)?><?=(mb_strlen($slide->title) > 30)?"...":""?>
                    </div>
                </div>
            </div>
        </div>
        <? } ?>
    </div>
    <!-- Панель слайдера для полноразмерной вёрстки -->
    <div class="info-slider__panel">
        <? foreach ($slides as $key => $slide) { ?>
        <div class="info-slider__panel-item">
            <div class="info-slider__panel-item-number">
                <div class="info-slider__panel-item-circle-wrap info-slider__panel-item-circle-wrap_left">
                    <div class="info-slider__panel-item-circle info-slider__panel-item-circle_left"></div>
                </div>
                <div class="info-slider__panel-item-circle-wrap info-slider__panel-item-circle-wrap_right">
                    <div class="info-slider__panel-item-circle info-slider__panel-item-circle_right"></div>
                </div>
                <?=++$key;?>
            </div>
            <div class="info-slider__panel-item-title">
                <?=$slide->getCurrentTagText()?>: <?=mb_substr($slide->title, 0, 28)?><?=(mb_strlen($slide->title) > 28)?"...":""?>
            </div>
        </div>
        <? } ?>
    </div>
</div>