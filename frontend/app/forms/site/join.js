/**
 * Обработчик формы регистрации.
 * @author Артём Широких kowapssupport@gmail.com
 */
$(function() {

    let join_form = $('.join-user-form');

    join_form
        .on("form.saved", function() {
            // переношу пользователя в кабинет.
            document.location.href = '/cabinet/product/add';
        })
    ;
});