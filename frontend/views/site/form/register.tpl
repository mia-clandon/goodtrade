{$form_start}
<div class="row">
    <div class="row">
        <h2 class="sub-title text-center text-lg-row-2x">Получите бесплатный доступ</h2>
    </div>

    {* Блок для вывода сообщений. Должен находиться в основном содержимом формы, под заголовком, но над всеми
       остальными элементами формы. Не должен при этом выходить за границы формы.
       Текст сообщения нужно помещать в блок с классом .form-message__text. Для каждой ошибки отдельный блок. *}
    <div class="form-message is-hidden">
        <div class="form-message__image"></div>
        <div class="form-message__close"></div>

        <p class="form-message__title">К сведению:</p>
        <p class="form-message__text">А вы знали, что можно зарегистрироваться бесплатно?</p>
        <p class="form-message__text">Бесплатная регистрация доступна по промо-коду. Подробности по ссылке: <a href="#">Бесплатная регистрация</a></p>
    </div>

    {* Если сообщение в виде предупреждения, то основному блоку нужно добавить класс-модификатор form-message_warning *}
    <div class="form-message form-message_warning is-hidden">
        <div class="form-message__image"></div>
        <div class="form-message__close"></div>

        <p class="form-message__title">Обратите внимание:</p>
        <p class="form-message__text">Вы не заполнили некоторые поля. Отсутствие данной информации может вызвать недоверие у ваших потенциальных партнёров.</p>
    </div>

    {* Если сообщение в виде ошибки, то основному блоку нужно добавить класс-модификатор form-message_error *}
    <div class="form-message form-message_error is-hidden">
        <div class="form-message__image"></div>
        <div class="form-message__close"></div>

        <p class="form-message__title">Критическая ошибка:</p>
        <p class="form-message__text">Не удалось получить ответ от сервера. Проверьте интернет соединение. Если проблема осталась, то обратитесь в техническую поддержку.</p>
    </div>


    {$phone}
    {$email}
    {$bin}
    {$submit}
    {$confidentiality}
    {$subscription}

</div>
{$form_end}