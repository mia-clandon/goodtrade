/**
 * Базовый файл для скриптов страницы.
 */
require([

    'jquery',
    'jquery-ui',
    'jquery.mask',
    'widgets/ui-layout',
    'widgets/ui-modal',
    'widgets/ui-commerce-offer',
    'widgets/ui-keeper',
    'widgets/ui-dropdown-menu',
    'widgets/ui-collapse',
    'widgets/ui-tumbler',
    'widgets/ui-checkbox',
    'widgets/ui-switcher',
    'widgets/jq-share',
    'widgets/ui-choice',
    'widgets/ui-callback',
    'widgets/jq-smooth-scroll',
    'validator',
    'notify',

], function($) {

    // скрипты разметки.
    $(document.body).layout();

    // форма регистрации - (маски инпутов регистрации).
    $('#input-tel').mask('+0 (000) 000 00 00');
    $('input[type="tel"]').mask('+0 (000) 000 00 00');
    $('#input-mail').mask("A", {
        translation: {
            "A": { pattern: /[\w@\-.+]/, recursive: true }
        }
    });
    $('#input-bin').mask('000000000000');

    // скрол.
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 1000);
                return false;
            }
        }
    });

    try {
        // ленивая загрузка фотографий.
        $("img.lazy").lazyload({effect : "fadeIn"});
    } catch (Exception) {
        //do nothing.
    }

    // popup'ы с коммерческим запросом.
    $('div[data-type="request"]').commerce();

    // модальные окна.
    $('#modal').modal();

    // выпадающий список.
    $('.dropdown').dropdownMenu();

    //todo разобрать;
    $('.js-keeper').keeper();

    // переключатели.
    $('.tumbler').tumbler({
        select: function(e, data) {
            var link = $(e.target).find('a').eq(data.value).attr('href');
            if (link.length > 0 && link !== '#') {
                window.location.href = link;
            }
        }
    });

    // js валидатор.
    $.validate({
        lang: 'ru',
        errorMessageClass: 'form-control-tip',
        borderColorOnError: false,
        errorElementClass: 'element-error'
    });

    $('.js-collapse').collapse();
    $('.jq-share').share();
    $('.ui-checkbox').checkbox();
    $('.ui-switcher').switcher();

    $('.jq-smooth-scroll').smoothScroll();
    $('.choice').choice();

    // popup с обратным звонком.
    $('div[data-type="callback"]').callback();

    // обработка восстановления пароля.
    $('.reset-password-form').on("form.saved", function () {
        $.notify("Проверьте почту.", "success");
        $('body').click();
    });

    // Кнопка закрытия у сообщений вверху формы
    $('.form-message__close').on("click", function () {
        $(this).parent(".form-message").addClass("is-hidden");
    });
});