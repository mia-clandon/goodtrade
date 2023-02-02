/**
 * Работа с обратным звонком.
 *
 */

import './ui-popup';
import './ui-spinner';
import './ui-select';
import './ui-input-city';
import './ui-choice';

export default $.widget('ui.callback', $.ui.popup, {
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