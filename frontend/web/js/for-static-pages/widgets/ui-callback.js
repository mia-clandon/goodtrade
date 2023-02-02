/**
 * Работа с обратным звонком.
 */
define([
    'jquery',
    'jquery-ui',
    'widgets/ui-popup',
    'widgets/ui-spinner',
    'widgets/ui-select',
    'widgets/ui-input-city',
    'widgets/ui-choice'
], function ($) {
    return $.widget('ui.callback', $.ui.popup, {
        options: {
            /** элемент при нажатии на который - будет открываться popup. */
            toggleSelector: '.popup-toggle.callback-popup',
            loaded_form: 0
        },
        _create: function () {
            this._super();
        },
        _init: function () {
            this._super();
        },
        show: function () {},
        hide: function () {}
    });
});