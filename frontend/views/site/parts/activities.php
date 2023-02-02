<?
/**
* @var array $activity_array
*/

$activities = count($activity_array);
$slides = ceil($activities / 3);
?>

<? for ($i = 0, $s = 0; $s < $slides; $s++) { ?>
    <div class="categories-slider__slide">
        <div class="elements-list">
        <? for ($j = 0; $i < $activities && $j < 3; $i++, $j++) { ?>
            <div class="elements-list__item col">
                <div class="elements-list__item-image elements-list__item-image_gray elements-list__item-image_<?= $activity_array[$i]['activity']->icon_class ?>"></div>
                <div class="elements-list__item-content">
                    <a href="<?=Yii::$app->urlManager->createUrl(['category/show', 'id' => $activity_array[$i]['activity']->id])?>" class="elements-list__item-title elements-list__item-title_one-line" title="<?= $activity_array[$i]['activity']->title ?>"><?= $activity_array[$i]['activity']->title ?></a>
                    <div class="elements-list__item-counters">
                        <span><?= $activity_array[$i]['firm_count'] ?> компании</span>
                        <span><?= $activity_array[$i]['product_count'] ?> товаров</span>
                    </div>
                </div>
            </div>
        <? } ?>
        </div>
    </div>
<? } ?>