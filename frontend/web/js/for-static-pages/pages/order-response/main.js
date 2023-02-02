require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'widgets/ui-layout',
    'widgets/ui-compare',
    'widgets/ui-select',
    'widgets/ui-modal',
    'widgets/ui-spinner',
    'widgets/ui-dropdown-menu',
    'widgets/ui-commerce-offer',
    'widgets/ui-choice',
    'widgets/ui-checkbox',
    'widgets/ui-popup',
    'widgets/ui-balance',
    'widgets/ui-settings',
    'widgets/ui-multiply',
], function($) {
    $(document.body).layout();

    // Маски для полей с дополнительными костылями из-за синхронизации полей через Knockout
    $('input[type="tel"]')
        .mask('+7 (700) 000 00 00')
        .on("keydown", function (e) {
            let acceptValues = [
                    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
                ],
                specialAcceptValues = [
                    "Backspace", "Delete", "Alt", "Ctrl", "Shift", "Tab", "ArrowLeft", "ArrowRight",
                    "F1", "F2", "F3", "F4", "F5", "F6", "F7", "F8", "F9", "F10", "F11", "F12"
                ];

            if ($.inArray(e.key, acceptValues) === -1 && $.inArray(e.key, specialAcceptValues) === -1) {
                return false;
            }

            // Не разрешаем ввод клавиш, кроме особых, если длина 18. Даже если символ среди допустимых
            if (e.target.value.length === 18) {
                if ($.inArray(e.key, specialAcceptValues) === -1) {
                    return false;
                }
            }
        });

    $('input[type="email"]')
        .mask("A", {
            translation: {
                "A": { pattern: /[\w@\-.+]/, recursive: true }
            }
        })
        .on("keydown", function (e) {
            let acceptValues = [
                "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
                "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
                "w", "x", "y", "z", ".", "_", "-",
                "Backspace", "Delete", "Alt", "Ctrl", "Shift", "Tab", "ArrowLeft", "ArrowRight",
                "F1", "F2", "F3", "F4", "F5", "F6", "F7", "F8", "F9", "F10", "F11", "F12"
            ];

            if ($.inArray(e.key, acceptValues) === -1) {
                return false;
            }
        });

    $('.input-bin')
        .mask('000000000000', {clearIfNotMatch: true})
        .on("keydown", function (e) {
            let acceptValues = [
                    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
                ],
                specialAcceptValues = [
                    "Backspace", "Delete", "Alt", "Ctrl", "Shift", "Tab", "ArrowLeft", "ArrowRight",
                    "F1", "F2", "F3", "F4", "F5", "F6", "F7", "F8", "F9", "F10", "F11", "F12"
                ];

            if ($.inArray(e.key, acceptValues) === -1 && $.inArray(e.key, specialAcceptValues) === -1) {
                return false;
            }

            // Не разрешаем ввод клавиш, кроме особых, если длина 12. Даже если символ среди допустимых
            if (e.target.value.length === 12) {
                if ($.inArray(e.key, specialAcceptValues) === -1) {
                    return false;
                }
            }
        });

    $('.input-price input')
        .mask('000 000 000 000 000 000 000')
        .on("keydown", function (e) {
            let acceptValues = [
                    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
                ],
                specialAcceptValues = [
                    "Backspace", "Delete", "Alt", "Ctrl", "Shift", "Tab", "ArrowLeft", "ArrowRight",
                    "F1", "F2", "F3", "F4", "F5", "F6", "F7", "F8", "F9", "F10", "F11", "F12"
                ];

            if ($.inArray(e.key, acceptValues) === -1 && $.inArray(e.key, specialAcceptValues) === -1) {
                return false;
            }

            // Не разрешаем ввод клавиш, кроме особых, если длина 27. Даже если символ среди допустимых
            if (e.target.value.length === 27) {
                if ($.inArray(e.key, specialAcceptValues) === -1) {
                    return false;
                }
            }
        });

    /*$('.popup-edit__multiply-list').multiply({
        max : 4,
        separateAddBtn: true,
        init: function (event, node) {
            let $inputs = $(node).find('input[type="email"]');
            $inputs.mask("A", {
                translation: {
                    "A": { pattern: /[\w@\-.+]/, recursive: true }
                }
            });
        }
    });*/

    $('.spinner').spinner();
    $('.mini-spinner').spinner();
    $('.select').select();
    $('.balance').balance();
    $('.settings').settings();

    $('#modal').modal();
    $('.dropdown').dropdownMenu();
    $('.choice').choice();
    $('.ui-checkbox').checkbox();

    $('.popup-edit_address').popup({
        // элемент при нажатии на который - будет открываться popup.
        toggleSelector: '.edit-control-icon-dropdown_address',
        // элемент, при нажатии на который будет будет происходить событие отмены
        cancelSelector: '.popup-edit__close'
    });

    $('.popup-edit_email').popup({
        toggleSelector: '.edit-control-icon-dropdown_email',
        cancelSelector: '.popup-edit__close'
    });

    $('.popup-edit_tel').popup({
        toggleSelector: '.edit-control-icon-dropdown_tel',
        cancelSelector: '.popup-edit__close'
    });

    $('.popup-edit_condition-delivery').popup({
        toggleSelector: '.edit-control-icon-pencil_condition-delivery',
        cancelSelector: '.popup-edit__title'
    });

    $('.popup-edit_condition-payment').popup({
        toggleSelector: '.edit-control-icon-pencil_condition-payment',
        cancelSelector: '.popup-edit__title'
    });

    $('.popup-edit_expires').popup({
        toggleSelector: '.edit-control-icon-dropdown_expires',
        cancelSelector: '.popup-edit__close'
    });

    $('.popup-edit_requisites').popup({
        toggleSelector: '.edit-control-icon-pencil_requisites',
        cancelSelector: '.popup-edit__title'
    });

    // Показ и скрытие серого блока с деталями запроса
    $("#wall-btn").on("click", function () {
        $(".row.wall").slideDown();
    });

    $(".row.wall .wall-close").on("click", function () {
        $(this).parent().slideUp();
    });
});