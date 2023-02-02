import '../common';

import '../../widgets/ui-compare';

$(function () {

    let $popup_date = $('#popup-date');

    /* Маска для поля даты, чтобы пользователь не смог ввести прошедшую дату */
    $popup_date.mask('');

    // Маска в виде функции для полей времени
    let maskBehavior = function (val) {
        val = val.split(":");
        return (parseInt(val[0]) > 19) ? "HZ:M0" : "H0:M0";
    };

    let sp_options = {
        onKeyPress: function(val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        },
        translation: {
            'H': { pattern: /[0-2]/, optional: false },
            'Z': { pattern: /[0-3]/, optional: false },
            'M': { pattern: /[0-5]/, optional: false }
        }
    };

    $('#popup-time-from').mask(maskBehavior, sp_options);
    $('#popup-time-to').mask(maskBehavior, sp_options);

    $('.spinner').spinner();

    // Выпадающий календарь в поле "Дата" всплывающего окна "Назначить встречу"
    $popup_date.datepicker({
        showAnim : 'slideDown',
        showOtherMonths: true,
        minDate : 0,
        prevText: "&#x3C;",
        nextText: "&#x3E;",
        monthNames: [ "Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь" ],
        monthNamesShort: [ "января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря" ],
        dayNamesMin: [ "Вс","Пн","Вт","Ср","Чт","Пт","Сб" ],
        dateFormat: "d M yy года",
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ""
    });

    // Всплывающее окно "Модификации"
    $('#popup-modifications').popup({
        // элемент при нажатии на который - будет открываться popup.
        toggleSelector: '.popup-toggle.modifications-popup',
        // элемент, при нажатии на который будет будет происходить событие отмены
        cancelSelector: '.popup-close'
    });

    // Всплывающее меню при нажатии на кнопку "Выслать контакты"
    $('#popup-menu').popup({
        // элемент при нажатии на который - будет открываться popup.
        toggleSelector: '.popup-toggle.send-contacts-popup',
        // элемент, при нажатии на который будет будет происходить событие отмены
        cancelSelector: '.popup-menu__close'
    });

    // Всплывающее окно коммерческого запроса. Пока popup, т.к. статичная страница
    $('#popup-commerce').popup({
        toggleSelector: '.popup-toggle.commercial-popup'
    });

    // Всплывающее окно общего коммерческого запроса. Пока popup, т.к. статичная страница
    $('#popup-commerce-all').popup({
        toggleSelector: '.popup-toggle.all-commercial-popup'
    });

    // Всплывающее окно "Назначить встречу"
    $('#popup-meeting').popup({
        toggleSelector: '.popup-toggle.meeting-popup'
    });

    $('.comparison-container').compare({});

    $('.js-keeper')
        .keeper()
        .on('updateCompare', function () {
            window.location.reload();
        });

    /**
     * Подгрузка контента продукта для сравнения.
     * @param product_ids
     * @param offset
     * @param callback
     */
    function loadProductList(product_ids, offset, callback) {
        let category_id = $('input[name=category]').val();

        $.ajax({
            url: '/compare/get-product-list',
            type: 'POST',
            data: {
                product_ids: product_ids,
                category_id: category_id,
                offset: offset,
            }
        }).done(function (data) {
            callback(data);

            $('.js-keeper')
                .keeper()
                .on('updateCompare', function () {
                    window.location.reload();
                });
        });
    }


    $('.js-add-compare').click(function () {
        let product_ids = $('.product_id').map(function(){return $(this).val();}).get();

        let offset = 0;

        loadProductList(product_ids, offset, function (data) {
            $(".js-suggest-content").html(data);

            $('.more_suggest_product').click(function () {
                let $self = $(this);
                let offset = $self.data('offset');

                let product_ids = $('.product_id').map(function(){return $(this).val();}).get();

                loadProductList(product_ids, offset, function (data) {
                    $(".js-suggest-content").append(data);

                    $self.data('offset', $self.data('offset') + $self.data('limit'));

                    if ($self.data('offset') >= $self.data('count')) {
                        $('.more_suggest_area').hide();
                    }
                });
            });

        });
    });
});