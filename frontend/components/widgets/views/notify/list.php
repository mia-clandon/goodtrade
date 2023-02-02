<?
/**
 * Список уведомлений.
 * @var string $notification_list
 * @var integer $notification_count
 */
?>
<li class="snippets-item dropdown">
    <a role="button" href="#" class="action dropdown-toggle">
        <span class="action-icon action-icon-notify">
            <span class="action-count"><?= $notification_count; ?></span>
        </span>
    </a>

<? if ($notification_count == 0) { ?>

    <div class="dropdown-menu dropdown-menu-notify dropdown-menu-notify_empty">
        <div class="dropdown-menu-head">
            <div class="dropdown-menu-title">Уведомления</div>
        </div>
        <div class="dropdown-menu-body">
            <div class="dropdown-menu-notify__image"></div>
            <div class="dropdown-menu-notify__text">
                <p>Сейчас у вас нет новых<br>уведомлений</p>
            </div>
        </div>
    </div>

<? } else { ?>

    <div class="dropdown-menu dropdown-menu-notify">
        <div class="dropdown-menu-head">
            <div class="dropdown-menu-title">Уведомления</div>
            <a href="#more" class="dropdown-menu-more">Смотреть все</a>
        </div>
        <div class="dropdown-menu-body has-scrollbar dropdown-menu-body-faded">
            <div class="dropdown-menu-content">
                <?= $notification_list; ?>
            </div>
<!--
            <div class="dropdown-menu-scrollbar">
                <div class="dropdown-menu-scrollbar-slider"></div>
            </div>
-->
        </div>
    </div>

<? } ?>
</li>