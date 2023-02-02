<?php
/**
 * @var int $favorite_count
 * @var array $favorite_data
 * @var bool $is_landing
 */
use frontend\components\widgets\Favorite;
use common\libs\Declension;
?>

<div class="dropdown b2b js-favorite-b2b">
    <div class="icon icon_favorite dropdown__toggle">
        <span class="icon-counter">
            <span id="favorite_count" class="icon-counter-value"><?= $favorite_count ?></span>
            <span class="icon-counter-background"><span></span></span>
        </span>
    </div>
    <div class="dropdown__item dropdown__item_bar-top-icon-center">
        <div class="dropdown__item-indicator"></div>
        <div class="modal">
            <div class="modal__title">Избранное</div>
            <?php if ($favorite_count == 0) { ?>
            <div class="notification notification_shifted-down">
                <div class="notification__image notification__image_favorite"></div>
                <p class="notification__description">Вы пока не добавили в избранное<br>ни одну компанию или товар</p>
                <a href="#" class="notification__link">Зачем нужно избранное?</a>
            </div>
            <? } else { ?>
            <div class="dropdown__item-body">
                <div class="dropdown__item-content">
                    <div class="elements-list">
                        <? foreach ($favorite_data as $favorite_item) { ?>
                        <div class="elements-list__item">
                            <div class="elements-list__item-content">
                                <div class="elements-list__item-title">
                                    <a href="<?= Yii::$app->urlManager->createUrl(['favorite/index', 'firm_id' => $favorite_item[Favorite::FIRM_ID_PROPERTY]])?>"><?= $favorite_item[Favorite::FIRM_TITLE_PROPERTY] ?></a>
                                </div>
                                <div class="elements-list__item-additional-text"><?= Declension::number($favorite_item[Favorite::PRODUCT_COUNT_PROPERTY], 'товар', 'товаров', 'товар', true);?></div>
                            </div>
                        </div>
                        <? } ?>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
    </div>
</div>