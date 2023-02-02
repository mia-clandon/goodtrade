<?php
/**
 * @var boolean $is_landing
 * @var int $compare_count
 * @var \common\models\goods\Product[] $compare_data
 */
use frontend\components\widgets\Compare;
use common\libs\Declension;
?>
<div class="dropdown b2b js-compare-b2b">
    <div class="icon icon_comparison dropdown__toggle">
        <span class="icon-counter">
            <span class="icon-counter-value"><?= $compare_count ?></span>
            <span class="icon-counter-background"><span></span></span>
        </span>
    </div>
    <div class="dropdown__item dropdown__item_bar-top-icon-center">
        <?php if ($compare_count == 0) { ?>
            <div class="dropdown__item-indicator"></div>
            <div class="modal">
                <div class="modal__title">Сравнение</div>
                <div class="notification notification_shifted-down">
                    <div class="notification__image notification__image_comparison"></div>
                    <p class="notification__description">Вы не создали<br>ни одного сравнения</p>
                    <a href="#" class="notification__link">Что такое сравнения?</a>
                </div>
            </div>
        <? } else { ?>
            <div class="dropdown__item-indicator"></div>
            <div class="modal">
                <div class="modal__title">Сравнение</div>
                <div class="dropdown__item-body">
                    <div class="dropdown__item-content">
                        <div class="elements-list">
                            <? foreach ($compare_data as $compare_item) { ?>
                            <div class="elements-list__item">
                                <div class="elements-list__item-content">
                                    <div class="elements-list__item-actions js-keeper" data-id="<?= $compare_item[Compare::PRODUCT_IDS_PROPERTY] ?>" data-key="compare<?=\Yii::$app->user->id?>" >
                                        <a href="#">Удалить сравнение</a>
                                    </div>
                                    <a class="elements-list__item-title" href="<?= Yii::$app->urlManager->createUrl(['compare', 'category' => $compare_item[Compare::CATEGORY_ID_PROPERTY]])?>">
                                        <?= $compare_item[Compare::CATEGORY_TITLE_PROPERTY];?>
                                    </a>
                                    <div class="elements-list__item-row">
                                        <!--<div class="breadcrumbs">
                                            <div class="breadcrumbs__item">
                                                <a href="#" class="breadcrumbs__item-icon breadcrumbs__item-icon_construction"></a>
                                                <div class="breadcrumbs__item-title"><a href="#">Строительство</a></div>
                                            </div>
                                        </div>-->
                                        <div class="elements-list__item-additional-text">
                                            <?= Declension::number($compare_item[Compare::PRODUCT_COUNT_PROPERTY], 'наименование', 'наименования', 'наименований', true);?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>