<?
/**
 * Заказ обратного звонка у организации.
 * @author Артём Широких kowapssupport@gmail.com
 */
?>
<div id="popup-commerce" data-type="callback" class="popup popup-form is-hidden">
    <div class="popup-head">
        <div class="popup-title">Заказать обратный звонок</div>
    </div>
    <div class="popup-body">
        <div class="popup-message popup-message-error">
            <div class="popup-message-text">Для того, чтобы отправить запрос на обратный звонок<br>вам нужно указать
                ваши имя и телефон.
            </div>
        </div>
        <form>
            <fieldset>
                <div class="form-control">
                    <div class="form-control-label">Введите ваше имя</div>
                    <div class="input input-name">
                        <input type="email" placeholder="Введите ваше имя" name="name[]" pattern="[a-z0-9._%+-]"/>
                    </div>
                </div>
                <div class="form-control">
                    <div class="form-control-label">Введите ваш телефон</div>
                    <div class="input input-tel">
                        <input type="tel" placeholder="+7 (777) 707 00 77" name="phone[]"/>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="popup-foot">
        <button id="popup-send" class="btn btn-blue">Заказать</button>
        <button id="popup-cancel" class="btn btn-link">Отменить</button>
    </div>
</div>