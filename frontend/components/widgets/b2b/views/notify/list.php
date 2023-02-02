<?
/**
 * Список уведомлений.
 * @var string $notification_list
 * @var integer $notification_count
 * @var bool $is_landing
 */
?>

<div class="dropdown b2b">
    <div class="icon icon_notice dropdown__toggle">
        <span class="icon-counter">
            <span class="icon-counter-value notify-counter"><?= $notification_count;?></span>
            <span class="icon-counter-background"><span></span></span>
        </span>
    </div>
    <div class="dropdown__item dropdown__item_bar-top-icon-center">
        <div class="dropdown__item-indicator"></div>
        <div class="modal">
            <div class="modal__title">Уведомления</div>
            <div class="dropdown__item-body">
                <div class="dropdown__item-content">
                    <? if ($notification_count == 0) { ?>
                        <div class="notification__image notification__image_notices"></div>
                        <p class="notification__description">Сейчас у вас нет новых<br>уведомлений</p>
                    <? } else { ?>
                        <div class="elements-list">
                            <?= $notification_list;?>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</div>