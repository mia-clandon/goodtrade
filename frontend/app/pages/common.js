import '../../../common/web/js/form';

// скрипты регистрации / авторизации.
import '../forms/site/register';
import '../forms/site/sign';

// используемые виджеты.
import '../widgets/ui-layout';
import '../widgets/ui-modal';
import '../widgets/ui-commerce-offer';
import '../widgets/ui-keeper';
import '../widgets/ui-dropdown-menu';
import '../widgets/ui-collapse';
import '../widgets/ui-tumbler';
import '../widgets/ui-checkbox';
import '../widgets/ui-switcher';
import '../widgets/jq-share';
import '../widgets/ui-choice';
import '../widgets/jq-smooth-scroll';
import '../widgets/ui-affix';
import '../widgets/ui-range';

$(function() {

    // скрипты разметки.
    $(document.body).layout();

    // форма регистрации - (маски инпутов регистрации).
    $('#input-tel').mask('+7 (000) 000 00 00');
    //$('input[type="tel"]').mask('+0 (000) 000 00 00');
    $('#input-mail').mask("A", {
        translation: {
            "A": { pattern: /[\w@\-.+]/, recursive: true }
        }
    });
    $('#input-bin').mask('000000000000');

    // Плавная прокрутка
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            let target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html, body').animate({
                    // Координата заголовка, минус верхняя плашка пользователя, минус плавающие вкладки
                    scrollTop: target.offset().top - 60 - 47
                }, 1000);
                return false;
            }
        }
    });

    try {
        // ленивая загрузка фотографий.
        $("img.lazy").lazyload({effect : "fadeIn"});
    }
    catch (Exception) {
        //do nothing.
    }

    // popup'ы с коммерческим запросом.
    $('[data-type="request"]').commerce();

    // модальные окна.
    $('#modal').modal();

    // выпадающий список.
    $('.dropdown').not('.b2b').dropdownMenu();

    function compare_widget() {
        let b2b = 0;
        if($('.js-compare-b2b').length > 0) {
            b2b = 1;
        }
        // update compare widget
        $.ajax({url: '/site/update-compare-widget', type: 'POST', data: {'b2b': b2b}, dataType: 'JSON'})
            .done(function(data) {
                if($('.js-compare-b2b').length > 0) {
                    $('.js-compare-b2b .dropdown__item_bar-top-icon-center').html(
                        $(data).find('.dropdown__item_bar-top-icon-center').html()
                    );

                    $('.js-compare-b2b .icon-counter').html($(data).find('.icon-counter').html());
                }
                else {
                    $('.dropdown-menu-compare .dropdown-menu-body').html(
                        $(data).find('.dropdown-menu-body').html()
                    );

                    if($('.dropdown-menu-compare .dropdown-menu-body ul').length > 0) {
                        $('.dropdown-menu-compare').removeClass('dropdown-menu-compare_empty');
                    }
                    else {
                        $('.dropdown-menu-compare').addClass('dropdown-menu-compare_empty');
                    }

                    $('li.snippets-item .action-icon-compare .action-count').html($(data).find('.action-count').html());

                }

                compare_counter();

                $('.js-compare-b2b .js-keeper')
                    .keeper()
                    .on('updateCompare', function (event, object) {
                        compare_widget();
                    });
            });
    }

    function compare_counter() {
        let counter_elem = null;
        if($('.js-compare-b2b').length > 0) {
            counter_elem = $('.js-compare-b2b .icon-counter');
        }
        else {
            counter_elem = $('li.snippets-item .action-icon-compare .action-count');
        }
        if (parseInt(counter_elem.text()) === 0) {
            counter_elem.hide();
        }
        else {
            counter_elem.show();
        }
    }

    function favorite_widget() {
        let b2b = 0;
        if($('.js-favorite-b2b').length > 0) {
            b2b = 1;
        }
        // update compare widget
        $.ajax({url: '/site/update-favorite-widget', type: 'POST', data: {'b2b': b2b}, dataType: 'JSON'})
            .done(function(data) {
                if($('.js-favorite-b2b').length > 0) {
                    let style = $('.js-favorite-b2b .dropdown__item_bar-top-icon-center .dropdown__item-indicator').attr("style");

                    $('.js-favorite-b2b .dropdown__item_bar-top-icon-center')
                        .html(
                            $(data).find('.dropdown__item_bar-top-icon-center').html()
                        )
                        .find('.dropdown__item-indicator').attr('style', style);

                    $('.js-favorite-b2b .icon-counter').html($(data).find('.icon-counter').html());
                }
                else {
                    $('.dropdown-menu-favorite .dropdown-menu-body').html(
                        $(data).find('.dropdown-menu-body').html()
                    );

                    $('li.snippets-item .action-icon-favorite .action-count').html($(data).find('.action-count').html());

                }

                favorite_counter();

                $('.js-favorite-b2b .js-keeper')
                    .keeper()
                    .on('updateCompare', function (event, object) {
                        favorite_widget();
                    });
            });
    }

    function favorite_counter() {
        let counter_elem = null;
        if($('.js-favorite-b2b').length > 0) {
            counter_elem = $('.js-favorite-b2b .icon-counter');
        }
        else {
            counter_elem = $('li.snippets-item .action-icon-favorite .action-count');
        }
        if (parseInt(counter_elem.text()) === 0) {
            counter_elem.hide();
        }
        else {
            counter_elem.show();
        }
    }

    function notify_counter() {
        if($('.b2b .notify-counter').length > 0) {
            let counter_elem = $('.b2b .notify-counter');
            if (parseInt(counter_elem.text()) === 0) {
                counter_elem.parent().hide();
            }
            else {
                counter_elem.parent().show();
            }
        }
    }

    compare_counter();
    favorite_counter();
    notify_counter();

    // добавление в cookies (товары/ избранное).
    $('.js-keeper')
        .keeper()
        .on('updateCompare', function (event, object) {
            let key = object.key;
            if (key.match("^compare")) {
                compare_widget();
            }
            else {
                favorite_widget();
            }
        });

    // переключатели.
    $('.tumbler').tumbler({
        select: function(e, data) {
            let link = $(e.target).find('a').eq(data.value).attr('href');
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

    //$('.js-collapse').collapse();
    $('.jq-share').share();
    $('.ui-checkbox').checkbox();
    $('.ui-switcher').switcher();

    $('.jq-smooth-scroll').smoothScroll();
    $('.choice').choice();

/*
    // popup с обратным звонком.
    $('div[data-type="callback"]').callback();
*/

    // обработка восстановления пароля.
    $('.reset-password-form').on("form.saved", function () {
        $.notify("Проверьте почту.", "success");
        $('body').click();
    });
    $.notify.addStyle("trade", {
        html: "<div>\n<span data-notify-text></span>\n</div>",
        classes: {}
    });
    $.notify.defaults({
        style: "trade",
        globalPosition: 'bottom left'
    });
});