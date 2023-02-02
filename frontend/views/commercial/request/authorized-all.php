<?

/**
 * Форма с коммерческим запросом для авторизованного пользователя.
 * @var string $form
 * @author yerganat
 */

?>
<div class="popup-head commercial-request-all">
    <div class="popup-title">Коммерческий запрос</div>
</div>
<div class="popup-body">
    <?= $form; ?>
    <div class="popup-message popup-message-error commercial-request-error">
        <div class="popup-message-title">Обратите внимание!</div>
        <div class="popup-message-text">Отправленные ранее коммерческие запросы не будут обновлены.</div>
    </div>
    <div class="popup-message commercial-request-success">
        <div class="popup-message-title">Поздравляем!</div>
        <div class="popup-message-text">Ваш коммерческий запрос отправлен.
        </div>
    </div>
</div>

<div class="popup-foot">
    <button id="popup-send" class="btn btn-blue">Отправить запрос</button>
    <button id="popup-cancel" class="btn btn-link">Отменить</button>
</div>