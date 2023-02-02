<?
/**
 * Форма с коммерческим запросом для не авторизованного пользователя.
 * @author Артём Широких kowapssupport@gmail.com
 */
?>

<div class="modal__title">Коммерческий запрос</div>
<div class="modal__notice">
    <span class="modal__notice-title">Обратите внимание</span>
    <div class="modal__notice-text">Для того, чтобы отправить коммерческий запрос вам нужно зарегистрироваться или <a href="#">авторизоваться</a>. Это позволяет обезопасить поставщиков и потребителей от мошенников и спама.</div>
</div>
<form action="">
    <div class="form-control">
        <div class="form-control__top-text">
            <div class="form-control__label">Ваш основной телефон</div>
        </div>
        <div class="input">
            <input type="text" placeholder="Ваш телефон">
        </div>
    </div>
    <div class="form-control">
        <div class="form-control__top-text">
            <div class="form-control__label">Ваш основной email</div>
        </div>
        <div class="input">
            <input type="text" placeholder="Ваш email">
        </div>
    </div>
    <div class="form-control">
        <button data-action="popup-send" class="button button_primary">
            <span class="button__text">Зарегистрироваться</span>
        </button>
        <button data-action="popup-close" class="button button_small button_link">
            <span class="button__text">Отменить</span>
        </button>
    </div>
    <div class="form-control">
        <div class="checkbox">
            <input type="checkbox" class="checkbox__input" checked>
            <label class="checkbox__label checkbox__label_small"><span class="checkbox__check-mark"></span>Я даю свое согласие на обработку персональных данных и соглашаюсь с политикой конфиденциальности</label>
        </div>
    </div>
    <div class="form-control">
        <div class="checkbox">
            <input type="checkbox" class="checkbox__input" checked>
            <label class="checkbox__label checkbox__label_small"><span class="checkbox__check-mark"></span>Я хочу получать email-письма о мероприятиях и/или иных услугах</label>
        </div>
    </div>
</form>