<?
/**
 * @var \common\models\MainSlider[] $slides
 * @var array $array_by_slide
 */

use common\models\firms\Firm;
use common\models\MainSlider;
use common\modules\image\helpers\Image as ImageHelper;

?>

<div class="block">
    <div class="block__content">
        <div class="main-slider-block">
            <div class="main-slider-block__arrow main-slider-block__arrow_left"></div>
            <div class="main-slider-block__arrow main-slider-block__arrow_right"></div>
            <div class="main-slider-block__container container container_main">
                <div class="main-slider row">
                    <? foreach ($slides as $slide) { ?>
                        <div class="main-slider__slide col-lg-8">
                        <div class="main-slider__slide-bg">
                            <? if (!empty($slide->image)) {
                                $image = ImageHelper::i()->generateRelativeImageLink($slide->image, 850, 500);
                                if ($image !== false) {
                                    echo \yii\helpers\Html::img($image);
                                }
                            }?>
                        </div>
                        <div class="main-slider__slide-content row">
                            <div class="col-lg-4">
                                <div class="main-slider__slide-title"><?= $slide->title ?></div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <p class="main-slider__slide-text"><?= strip_tags($slide->description) ?></p>
                                        <a href="<?= $slide->link ?>" class="button button_primary">
                                            <span class="button__text"><?= $slide->button ?></span>
                                        </a>
                                        <div class="main-slider__slide-button-desc"><?= $slide->tip ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="cards-list cards-list_lg cards-list_vertical cards-list_in-slider row">
                                    <?
                                    /**
                                     * @var MainSlider $part
                                     */
                                    $is_first_vertical_square = false;

                                    foreach ($array_by_slide[$slide->id] as $key => $part) {
                                        if($key == 0 && $part->type == MainSlider::SLIDER_VERTICAL_SQUARE) {
                                            $is_first_vertical_square = true;
                                        }

                                        //Если первый вертикальный а остальные элементы с размером 1x1 показываем внутри col-lg-4(т.к. в админке для вериткального горизонтальный добавить не возможно)
                                        if($is_first_vertical_square && $key > 0 && $part->type != MainSlider::SLIDER_VERTICAL_SQUARE) {
                                            break;
                                        }
                                        switch ($part->tag) {
                                            case MainSlider::TAG_BANNER:
                                                echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/slides/banner.php'), [
                                                    'slide' => $part,
                                                    'show_col' => true,
                                                ]);
                                                break;
                                            case MainSlider::TAG_NEW_FIRM:
                                                $firm = Firm::findOne(['id' => $part->firm_id]);
                                                if($firm) {
                                                    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/slides/firm.php'), [
                                                        'slide' => $part,
                                                        'firm' => $firm,
                                                        'show_col' => true,
                                                    ]);
                                                }
                                                break;
                                        }
                                    }

                                    if($is_first_vertical_square) {
                                        echo "<div class='col-lg-4'>";
                                        foreach ($array_by_slide[$slide->id] as $key => $part) {
                                            if ($key == 0) {
                                                continue;
                                            }
                                            switch ($part->tag) {
                                                case MainSlider::TAG_BANNER:
                                                    echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/slides/banner.php'), [
                                                        'slide' => $part,
                                                        'show_col' => false,
                                                    ]);
                                                    break;
                                                case MainSlider::TAG_NEW_FIRM:
                                                    $firm = Firm::findOne(['id' => $part->firm_id]);
                                                    if($firm) {
                                                        echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/site/parts/slides/firm.php'), [
                                                            'slide' => $part,
                                                            'firm' => $firm,
                                                            'show_col' => false,
                                                        ]);
                                                    }
                                                    break;
                                            }
                                        }
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</div>