/**
 * Виджет выбора конфигурации.
 * @author Kenzhegulov Madiyar
 * @version 1.0.0
 * @exports widgets/ui-choice
 * @example Пример использования виджета
 * // $('.choice').choice();
 */
export default $.widget('ui.choice', [$.ui.formResetMixin, {
    options: {
        value: 0,
        selected: -1,
        paramName: ''
    },
    _create: function () {
        //noinspection JSUnresolvedVariable
        var $hidden = this.element.children('input[type="hidden"]'),
            $items = this.element.children('.choice-item'),
            paramName = this.element.data('name') || this.options.paramName,
            defaultValue = this.element.data('value') || this.options.value,
            defaultSelected = this.element.data('selected') || this.options.selected;
        //Setting available max value and attach element to the property
        if ($items.length) {
            this.items = $items;
        }
        // If hidden input exists
        if ($hidden.length) {
            //then attach to property
            this.hidden = $hidden;
            //Override default value
            defaultValue = $hidden.val() || defaultValue;
            //Override paramName
            paramName = $hidden.attr('name') || paramName;
            //Override selected
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
        //TODO:
        //this.select(defaultSelected);
    },
    _getCreateEventData: function () {
        return this.options;
    },
    _init: function () {
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        this._bindFormResetHandler();
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        this._on(this.element, {'click .choice-item': '_handleClick'});
    },
    _destroy: function () {
        //noinspection JSUnresolvedFunction
        this._unBindFormResetHandler();
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        this._off(this.element, 'click');
    },
    refresh: function () {
        this.option('selected', -1);
    },
    _handleClick: function (event) {
        this.select($(event.currentTarget).index());
        event.preventDefault();
    },
    /**
     * @param {Number} index
     * @param {Event} event
     * @event select
     */
    select: function (index, event) {
        //noinspection JSUnresolvedVariable
        var value = this.items
            .eq(index)
            .data('value');

        //noinspection JSUnresolvedFunction
        this.option({
            selected: index,
            value: value
        });
        //noinspection JSUnresolvedFunction
        this._trigger('select', event || null, {index: index, value: value});
    },
    _setOption: function (key, val) {
        if (key === 'selected') {
            if (typeof val === 'number' && val !== -1) {
                //noinspection JSUnresolvedFunction
                this._removeClass(this.items, 'choice-item-active');
                //noinspection JSUnresolvedFunction
                this._addClass(this.items.eq(val), 'choice-item-active');
            }
            else {
                //noinspection JSUnresolvedFunction
                this._removeClass(this.items, 'choice-item-active');
            }
        }
        else if (key === 'value') {
            this.hidden.val(val);
        }
        else if (key === 'paramName') {
            this.hidden.attr('name', val);
        }
        //noinspection JSUnresolvedFunction
        return this._super(key, val);
    }
}]);