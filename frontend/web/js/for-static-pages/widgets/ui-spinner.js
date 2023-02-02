/*
 * Виджет для увеличения или уменьшения количества чего-либо.
 * Например, для задания количества штук товара в партии.
 * На сайте встречается в виде горизонтального блока с кнопка минуса слева
 * и кнопкой плюса справа
 */
define(['jquery','jquery-ui'],function($) {
    return $.widget('ui.spinner',{
        options : {
            value : 0,
            input : null,
            max : 9999,
            min : 0,
            timer : null,
            delay : 500
        },
        _create : function () {
            let $root = this.element,
                $input = this.options.input = $root.find('input');

            this._on($root, {'mousedown a' : this._handleMouseDown});
            this._on(document.body, {'mouseup': this._handleMouseUp});
            this._on($input, {
                change : this._handleChange,
                keydown : this._handleKeyDown,
                keyup : this._handleKeyUp
            });
        },
        _init : function () {
            this.options.value = this.options.input.val();
        },
        _destroy : function() {
            let $root = this.element,
                $input = this.option('input');

            this._off($input, 'change');
            this._off($input, 'reset');
            this._off($input, 'keydown');
            this._off($input, 'keyup');
            this._off($root, 'mousedown a');
        },
        refresh : function() {
            this.option('value', 0);
        },
        _handleMouseDown : function (e) {
            let value = this.option('value'),
                $el = $(e.target),
                action = $el.data('action'),
                timer = this.option('timer'),
                delay = this.option('delay'),
                self = this;

            if (e.which !== 1) return false;
            if (action === 'increase') {
                this.option('value', ++value);
            } else if (action === 'decrease') {
                this.option('value', --value);
            }
            timer = this._delay(function() {
                self._handleMouseDown(e)
            }, Math.max(delay,100));
            this.option({
                timer : timer,
                delay : delay - 50
            });
            this._trigger('change', null, {value : Math.max(0,value)});
            e.preventDefault();
        },
        _handleMouseUp : function(e) {
            let timer = this.option('timer');
            clearTimeout(timer);
            this.option('delay',500);
        },
        _handleChange : function(e) {
            let value =  e.target.value,
                currentValue = this.option('value');

            this.option('value', isNaN((value)) ? currentValue : value);
        },
        _handleKeyDown : function(e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        },
        _handleKeyUp : function(e) {
            let $el = $(e.target);
            this.option('value', parseInt($el.val(), 10));
        },
        _setOption : function(key, val) {
            let $input = this.option('input'),
                max = this.option('max'),
                min = this.option('min'),
                value = Math.min(max, val);

            if (key === 'value')  {
                if (val < min) { return }
                $input.val(value);
                //$input.focus();
                // Генерируем событие input вручную. Необходимо для взаимодействия с другими фреймворками
                // (например, с Knockout.js)
                let evt = document.createEvent("Event");
                evt.initEvent("input", false, true);
                $input[0].dispatchEvent(evt);
            }
            return this._super(key, value);
        }
    });
});