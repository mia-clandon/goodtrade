require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'slick',
    'jquery.nanoscroller',
    'widgets/ui-layout',
    'widgets/ui-compare',
    //'widgets/ui-select',
    'widgets/ui-modal',
    'widgets/ui-spinner',
    'widgets/jq-smooth-scroll',
    'widgets/ui-dropdown-menu',
    'widgets/ui-commerce-offer',
    'widgets/ui-choice',
    'widgets/ui-checkbox',
    'widgets/ui-popup'
], function($) {
    $(document.body).layout();
    $('#input-tel').mask('+0 (000) 000 00 00');
    $('#input-mail').mask("A", {
        translation: {
            "A": { pattern: /[\w@\-.+]/, recursive: true }
        }
    });
    $('#input-bin').mask('000000000000');
    
    /* Маска для поля даты, чтобы пользователь не смог ввести прошедшую дату */
    $('#popup-date').mask('');
        
    // Маска в виде функции для полей времени
    let maskBehavior = function (val) {
        val = val.split(":");
        return (parseInt(val[0]) > 19) ? "HZ:M0" : "H0:M0";
    };

    let spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        },
        translation: {
            'H': { pattern: /[0-2]/, optional: false },
            'Z': { pattern: /[0-3]/, optional: false },
            'M': { pattern: /[0-5]/, optional: false }
        }
    };

    $('#popup-time-from').mask(maskBehavior, spOptions);
    $('#popup-time-to').mask(maskBehavior, spOptions);


    $('.spinner').spinner();
    //$('.select').select();
    
    $('#modal').modal();
    $('.jq-smooth-scroll').smoothScroll();
    $('#feedback').slick({
        accessibility: false,
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 2000,
        arrows: false,
        dots: true,
        dotsClass: 'feedback-dots',
        draggable: true,
        infinite: true
    });
    $('.dropdown').dropdownMenu();
    $('.choice').choice();
    $('.ui-checkbox').checkbox();
    
    // Выпадающий календарь в поле "Дата" всплывающего окна "Назначить встречу"
    $('#popup-date').datepicker({
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
});