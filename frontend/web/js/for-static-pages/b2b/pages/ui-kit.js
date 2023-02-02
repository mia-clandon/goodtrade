require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'widgets/ui-layout',
    //'widgets/ui-compare',
    'widgets/ui-select-b2b',
    'widgets/ui-modal',
    'widgets/ui-dropdown-menu',
    //'widgets/ui-choice',
    'widgets/ui-checkbox',
    'widgets/ui-popup',
    //'widgets/ui-settings',
    //'widgets/ui-multiply',
    'widgets/ui-range',
    'widgets/ui-commerce-offer-b2b',
], function($) {
    $(".checkbox").checkbox({
        checkedClass: "checkbox_checked"
    });

    $(".range_value").range({
        type: "value"
    });

    $(".range_balance").range({
        type: "balance"
    });

    $(".range").not(".range_value", ".range_balance").range();

    $(".select").select();

    $('[data-type="request"]').commerce({
        // элемент при нажатии на который - будет открываться popup.
        toggleSelector: '[data-action="popup-toggle"]',
        // элемент, внутри которого находится переключатель и будет появляеться модальное окно
        wrapperSelector: '[data-type="popup-wrapper"]',
        // элемент, при нажатии на который будет будет происходить событие отмены
        cancelSelector: '[data-action="popup-close"]'
    });
});