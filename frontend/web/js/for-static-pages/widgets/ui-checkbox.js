define('widgets/ui-checkbox', ['jquery', 'jquery-ui'], function($) {
    /**
     * Виджет чекбокса
     * @version 1.0.0
     * @example Пример использования виджета
     * // $('.ui-checkbox').checkbox();
     * @exports widgets/ui-checkbox
     */
    $.widget('ui.checkbox', [ $.ui.formResetMixin, {
        options : {
            checked : false,
            required : false,
            checkedClass : 'ui-checkbox-checked'
        },
        _create : function() {
            this.label = this.element.children('label');
            this.checkbox = this.element.children(':checkbox');
        },
        _init : function() {
            var self = this,
                checkboxId = this.checkbox.uniqueId(),
                required = this.checkbox.prop('required') ||
                    this.element.data('required') ||
                    this.options.required,
                checked = this.checkbox.prop('checked') ||
                    this.element.data('checked') ||
                    this.options.checked;

            this.defaultState = {required : required, checked : checked};
            this.setState(checked);

            if(this.label.length && checkboxId.length) {
                this.label.attr('for', checkboxId[0].id);
            }
            this._on(this.element, {'click' : '_handleClick'});
            this._bindFormResetHandler();

            // Отслеживаем событие clickedOutside на флажке, генерируемое с помощью Knockout.js
            this.checkbox.on("clickedOutside", function (event) {
                self._handleClick(event, true);
            });
        },
        _destroy : function() {
            if(this.label.length) {
                this._off(this.label, 'click');
            } else {
                this._off(this.element, 'click');
            }
            this._unbindFormResetHandler();
        },
        _handleClick : function(event, outside) {
            if (this.checkbox[0].disabled) {
                return false;
            }

            var outside = outside || false;

            this.setState(!this.options.checked);
            event.preventDefault();

            // Генерируем событие click вручную. Необходимо для взаимодействия с другими фреймворками
            // (например, с Knockout.js). Если метод вызван событием clickedOutside выше, то событие не генерируем
            // иначе будет бесконечная рекурсия.
            if (!outside) {
                let evt = document.createEvent("Event");
                evt.initEvent("click", false, true);
                this.checkbox[0].dispatchEvent(evt);
            }
        },
        refresh : function() {
            this.setState(this.defaultState);
        },
        /**
         * @param {Boolean} state
         * @param event change
         */
        setState : function(state, event) {
            var value = this.checkbox.val();
            this.option('checked', state);
            this._trigger('change', event || null, {state: state, value : value });
        },
        _setOption : function(key, val) {
            if(key === 'checked') {
                this.checkbox.prop('checked', val);
                if(val) {
                    this._addClass(this.options.checkedClass);
                } else {
                    this._removeClass(this.options.checkedClass);
                }
            }
            return this._super(key, val);
        }
    }]);
});