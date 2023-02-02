/**
 * Виджет тамблер
 * @author Kenzhegulov Madiyar
 * @version 1.0.0
 * @example Пример использования виджета
 * // $('.tumbler').tumbler();
 * // Пример разметки
 * // <div class="tumbler">
 * // <button class="tumbler-button">Кнопка 1</button>
 * // <button>Кнопка 2</button>
 * // </div>
 * @exports widgets/ui-tumbler
 * TODO : Добавить WAI-ARIA разметку
 */
export default $.widget('ui.tumbler', [$.ui.formResetMixin, {
    options: {
        paramName: 'tumbler',
        value: 0
    },
    _create: function () {

        //noinspection JSUnresolvedVariable
        let $hidden = this.element.children('input[type="hidden"]'),
            $buttons = this.element.children('.tumbler-button'),
            paramName = this.element.data('name') || this.options.paramName,
            defaultValue = this.element.data('value') || this.options.value;

        //Setting available max value and attach element to the property
        if ($buttons.length) {
            this.buttons = $buttons;
            this.options.maxValue = ~-$buttons.length;
        }
        // If hidden input exists
        if ($hidden.length) {
            //then attach to property
            this.hidden = $hidden;
            //Override default value
            defaultValue = $hidden.val() || defaultValue;
            //Override paramName
            paramName = $hidden.attr('name') || paramName;
        }
        else {
            // else create new hidden input
            //noinspection JSUnresolvedVariable
            this.hidden = $('<input/>', {'type': 'hidden'}).appendTo(this.element);
        }

        //Setting default value from DOM element
        if (defaultValue !== undefined && typeof defaultValue === 'number') {
            //noinspection JSUnresolvedFunction
            this.option('value', defaultValue);
        }
        //Setting name from DOM element
        if (paramName !== undefined && typeof paramName === 'string') {
            //noinspection JSUnresolvedFunction
            this.option('paramName', paramName);
        }
    },
    _getCreateEventData: function () {
        return this.options;
    },
    _init: function () {
        //noinspection JSUnresolvedFunction
        this._bindFormResetHandler();
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        this._on(this.element, {'click .tumbler-button': '_handleClick'});
    },
    _destroy: function () {
        //noinspection JSUnresolvedFunction
        this._unbindFormResetHandler();
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        this._off(this.element, 'click');
    },
    refresh: function () {
        //noinspection JSUnresolvedFunction
        this.option('value', 0);
    },
    _handleClick: function (event) {
        let index = $(event.currentTarget).index();
        this.select(index, event);
        event.preventDefault();
    },
    /**
     * @param {Number} index
     * @param {Event} event
     * @event select
     */
    select: function (index, event) {
        //noinspection JSUnresolvedVariable
        let data = this.buttons
            .eq(index)
            .data();
        //noinspection JSUnresolvedFunction
        this.option('value', index);
        //noinspection JSUnresolvedFunction
        this._trigger('select', event, {value: index, data: data});
    },
    _setOption: function (key, val) {
        if (key === 'value') {
            if (typeof val === 'number') {
                //noinspection JSUnresolvedFunction
                this._removeClass(this.buttons, 'tumbler-button-active');
                //noinspection JSUnresolvedFunction
                this._addClass(this.buttons.eq(val), 'tumbler-button-active');
                this.hidden.val(val);
            }
        }
        else if (key === 'paramName') {
            this.hidden.attr('name', val);
        }
        //noinspection JSUnresolvedFunction
        return this._super(key, val);
    }
}]);