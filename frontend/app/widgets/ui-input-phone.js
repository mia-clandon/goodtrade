import './ui-input';

export default $.widget('ui.inputPhone', {
    options: {
        inputFields: null,
        inputHidden: null,
        phone: []
    },
    _create: function () {
        this._on(this.element, {'keyup': this._handleKeyUp});
        this.options.inputFields = this.element.find('.input-field');
        this.options.inputHidden = this.element.find('input[type="hidden"]');
    },
    _init: function () {
        this.options.phone = [];
    },
    _handleKeyUp: function (e) {
        var $el = $(e.target),
            phone = this.option('phone'),
            index = $el.data('index'),
            max = ~-this.options.inputFields.length;
        phone[index] = $el.val();
        this.option('phone', phone);
        if (e.keyCode == $.ui.keyCode.BACKSPACE && $el.val() == '' && index != 0) {
            this.options.inputFields[--index].focus();
            return false;
        }

        if ($el.attr('maxlength') == $el.val().length && index != max) {
            this.options.inputFields[++index].focus();
        }
    },
    _setOption: function (key, val) {
        this._super(key, val);
        this._refresh();
    },
    _refresh: function () {
        var hidden = this.option('inputHidden'),
            phone = this.option('phone');
        hidden.val(phone.join(''));
    }
});