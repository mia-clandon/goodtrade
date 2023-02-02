export default $.widget('ui.input', {
    options: {
        value: '',
        inputField: null,

        selectors: {
            inputField: '.input-field',
        }
    },
    _create: function () {
        var $root = this.element,
            $input = this.options.inputField = $(this.options.selectors.inputField, $root);
        this._on($input, {keyup: this._handleKeyUp});
        this._on($input, {
            'paste': this._handleCutAndPaste,
            'cut': this._handleCutAndPaste
        });
    },
    _init: function () {
        var $input = this.option('inputField');
        this.options.value = $input.val();
    },
    _destroy: function () {
        var $input = this.option('inputField');
        this._off($input, 'cut');
        this._off($input, 'paste');
        this._off($input, 'keyup');
    },
    refresh: function () {
        this._setValue('');
    },
    _handleKeyUp: function (event) {
        var value = event.target.value;
        if ((event.which <= 90 && event.which >= 48) || (event.keyCode == $.ui.keyCode.BACKSPACE)) {
            this.option('value', value);
        }
    },
    _handleCutAndPaste: function (event) {
        var value = event.target.value;
        this.option('value', value);
    },
    _setValue: function (value) {
        var $input = this.option('inputField');
        $input.val(value);
        this.options.value = value;
    }
});