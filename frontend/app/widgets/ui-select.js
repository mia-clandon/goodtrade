/**
 * Виджет Select
 * @example Пример использования виджета
 * // $('.select').select();
 * @author Kenzhegulov Madiyar, Selyutskiy Vikentiy
 * @exports widgets/ui-select
 */
export default $.widget('ui.select', [$.ui.formResetMixin, {
    options: {
        isActive: false,
        isEditable: false
    },
    _create: function () {
        this.selectElem = this.element.find('select');
        var $first_selected_value = this.selectElem.find('option:first').val();
        this.hidden = $('<input/>', {'type': 'hidden', 'value': $first_selected_value});
        this.list = $('<ul/>', {'class': 'select-list'});
        this._bindFormResetHandler();
    },
    _init: function () {
        var self = this,
            paramName = this.selectElem.attr('name');
        this.selectElem.hide();
        this.hidden.attr('name', paramName);
        this.button = this._renderButton();
        this._on(this.button, {'click': '_handleButtonClick'});

        this.element.append(this.button, this.hidden);
        this._on(this.list, {
            'click li': '_handleItemClick'
        });
        this.options.isEditable = this.selectElem.data('editable') || this.options.isEditable;
        if (this.options.isEditable === true) {
            this._on(this.list, {
                'change': '_handleChange',
                'keydown': '_handleKeyDown'
            });
        }
        this.refresh();

        // Отслеживаем событие changedOutside на выпадающем списке, генерируемое с помощью Knockout.js
        this.selectElem.on("changedOutside", function (event) {
            let optionVal = event.target.selectedOptions[0].value;

            self.list.children("li").each(function (index, element) {
                let liVal = $(element).data("value").value;

                if (liVal === optionVal) {
                    self.select(index, true);
                    return false;
                }
            });
        });
    },
    _destroy: function () {
        // Если на селекте имеется атрибут data-editable="true"
        if (this.options.isEditable) {
            //noinspection JSUnresolvedFunction
            this._off(this.list, 'change');
            //noinspection JSUnresolvedFunction
            this._off(this.list, 'keydown');
        }
        //noinspection JSUnresolvedFunction
        this._off(this.list, 'click');
        //noinspection JSUnresolvedFunction
        this._off(this.button, 'click');
        this.button.remove();
        this.hidden.remove();
        this.list.remove();
        this.selectElem.show();
        //noinspection JSUnresolvedFunction
        this._unbindFormResetHandler();
    },
    refresh: function () {
        this._renderMenu();
    },
    /**
     * Открыть меню
     * @event open
     * @public
     */
    open: function () {
        var self = this;
        //noinspection JSUnresolvedFunction
        this._addClass('is-active');
        $('body').on('click', function (event) {
            var $el = $(event.target);
            if (self.element.is($el) || self.element.has($el).length) {
                return false;
            } else {
                $('body').off(event);
                self.option('isActive', false);
            }
        });

        //Фокус на первый элемент
        if (this.options.isEditable === true) {
            //noinspection JSUnresolvedVariable
            var $input = this.list
                    .children('li:first')
                    .find('input'),
                value = $input.val();
            $input.focus()
                .val('')
                .val(value);
        }
        //noinspection JSUnresolvedFunction
        this._trigger('open');
    },
    /**
     * Закрыть меню
     * @event close
     * @public
     */
    close: function () {
        //noinspection JSUnresolvedFunction
        this._removeClass('is-active');
        //noinspection JSUnresolvedFunction
        this._trigger('close');
    },
    /**
     * Выбирает элемент из списка по индексу
     * @param {Number} index Индекс элемента который необходимо выбрать
     * @param {Boolean} outside Флаг для определения, если метод был вызван событием changedOutside выше
     * @event select
     * @public
     */
    select: function (index, outside) {
        var outside = outside || false,
            $list = this.list,
            $li = $list.children('li'),
            $first = $li.first(),
            $target = $li.eq(index),
            data = $target.data('value'),
            value = data.value,
            text = data.text;

        if (this.options.isEditable) {
            // Если элемент изменялся
            if ($first.data('value').custom === true) {
                $first.remove();
            } else {
                $first.remove('input')
                    .text($first.data('value').text);
            }
            $target.text('');
            $('<input/>', {
                'type': 'text',
                'value': text
            }).appendTo($target);
        }
        $target.prependTo($list);
        this._setData(data);
        this._trigger('select', null, value);

        // Генерируем событие change вручную. Необходимо для взаимодействия с другими фреймворками
        // (например, с Knockout.js). Если метод вызван событием changedOutside выше, то событие не генерируем
        // иначе будет бесконечная рекурсия.
        if (!outside) {
            this.selectElem.val(value).change();
        }
        /*
        let evt = document.createEvent("Event");
        evt.initEvent("change", false, true);
        this.selectElem[0].dispatchEvent(evt);
        */
        this.option('isActive', false);
    },
    _handleChange: function (event) {
        var $el = $(event.target),
            $li = $el.parent('li'),
            data = $li.data('value'),
            value = $el.val();
        $li
            .data('value', $.extend(data, {
                value: value
            }));
    },
    _handleKeyDown: function (event) {
        if ((event.which <= 90 && event.which >= 48) || (event.keyCode === $.ui.keyCode.BACKSPACE)) {
            var $input = $(event.target),
                $li = $input.parent('li'),
                data = $li.data('value');
            if (data.custom !== true) {
                var $clone = $li.clone();
                $clone.data('value', {custom: true});
                this.list.before($clone);
                this._renderMenu()
            }
        }
    },
    /**
     * Обработчик клика по кнопке
     * @param event
     * @private
     */
    _handleButtonClick: function (event) {
        //noinspection JSUnresolvedFunction
        this.option('isActive', true);
        event.preventDefault();
    },
    /**
     * Обработчик клика по элементу из списка
     * @param event
     * @private
     */
    _handleItemClick: function (event) {
        var index = $(event.target).index();
        if (index !== 0) {
            this.select(index);
        } else {
            this.close();
        }
        event.preventDefault();
    },
    _setData: function (data) {
        this.hidden.val(data.value);
        this.button.text(data.text);
    },
    /**
     * Рендеринг меню
     * @private
     */
    _renderMenu: function () {
        var self = this,
            $options = this.selectElem.find('option'),
            selectIndex = $options.filter(':selected').index(),
            $ul = $(this.list)
                .detach()
                .empty();
        $.each($options, function (index, item) {
            var isSelected = (index === selectIndex),
                $item = $(item);
            self._renderItemData($ul, $item, index, isSelected).appendTo($ul);
        });
        this.element.append($ul);
        this.select(selectIndex);
    },
    /**
     * Рендеринг кнопки
     * @returns {jQuery} Возвращает jQuery обертку элемента <a>
     * @private
     */
    _renderButton: function () {
        var $first_option = this.selectElem.find('option:first'),
            $first_option_text = $first_option.text(),
            $button = $('<a/>', {'role': 'button', 'href': '#'})
                .text($first_option_text);
        //noinspection JSUnresolvedFunction
        this._addClass($button, 'select-button');
        return $button;
    },
    /**
     *
     * @param {jQuery} $ul Элемент <ul> в обертке jQuery
     * @param {jQuery} $item Элемент <li> в обертке  jQuery
     * @param {Number} index Индекс элемента
     * @param {Boolean} selected
     * @returns {jQuery}
     * @private
     */
    _renderItemData: function ($ul, $item, index, selected) {
        var $li = $('<li/>')
            .data('value', {
                value: $item.val(),
                text: $item.text(),
                data: $item.data()
            });
        //noinspection JSUnresolvedFunction
        this._addClass($li, 'select-list-item');
        return this._renderItem($ul, $li, index, selected)
    },
    /**
     * Рендеринг элемента списка
     * @param {jQuery} $ul Объект элемента списка
     * @param {jQuery} $li Элемент <option> из <select>
     * @param {Number} index Индекс текущего элемента <option>
     * @param {Boolean} selected
     * @returns {jQuery} Возращает обертку jQuery элемента <li>
     * @private
     */
    _renderItem: function ($ul, $li, index, selected) {
        var text = $li.data('value').text;
        if (this.options.isEditable === true && index === 0) {
            $('<input/>', {
                'type': 'text',
                'value': text
            }).appendTo($li);
        } else {
            $li.text(text);
        }
        return $li;
    },
    _setOption: function (key, val) {
        if (key === 'isActive') {
            if (val) {
                this.open();
            } else {
                this.close();
            }
        }
        //noinspection JSUnresolvedFunction
        return this._super(key, val);
    }
}]);