/**
 * Виджет Select
 * @example Пример использования виджета
 * // $('.select').select();
 * @author Kenzhegulov Madiyar, Selyutskiy Vikentiy
 * @exports widgets/ui-select
 */
export default $.widget('ui.select', {
    options: {
        isActive: false,

        classes: {
            selectButton: "select__button",
            selectPlaceholder: "select__placeholder",
            selectText: "select__text",
            selectDropdown: "select__dropdown",
            selectList: "select__list",
            selectListItem: "select__list-item",
            isActive: "is-active"
        }
    },

    _create: function () {
        this.selectElem = this.element.find('select');
        var firstSelectedValue = this.selectElem.find('option:first').val();
        this.hidden = $('<input/>', {'type': 'hidden', 'value': firstSelectedValue});
        this.list = $('<ul>', {'class': this.options.classes.selectList});
        this.data = {
            placeholderText : this.selectElem[0].dataset.placeholderText,
        };
        this.dropdown = $('<div>', {'class': this.options.classes.selectDropdown});

        this.dropdown.append( $('<div>', {'class': 'modal'}) );
    },

    _init: function () {
        var self = this,
            paramName = this.selectElem.attr('name');

        this.selectElem.hide();
        this.hidden.attr('name', paramName);
        this.button = this._renderButton();
        this.placeholder = this.button.children("." + this.options.classes.selectPlaceholder),
            this.selectText = this.button.children("." + this.options.classes.selectText),
            this._on(this.button, {'click': '_handleButtonClick'});

        this.element.append(this.button, this.hidden);
        this._on(this.list, {
            'click li': '_handleItemClick'
        });
        this.refresh();

        // Отслеживаем событие changedOutside на выпадающем списке, генерируемое с помощью Knockout.js
        this.selectElem.on("changedOutside", function (event) {
            var optionVal = event.target.selectedOptions[0].value;

            self.list.children("li").each(function (index, element) {
                var liVal = $(element).data("value").value;

                if (liVal === optionVal) {
                    self.select(index, true);
                    return false;
                }
            });
        });
    },

    _destroy: function () {
        this._off(this.list, 'click');
        this._off(this.button, 'click');
        this.button.remove();
        this.hidden.remove();
        this.list.remove();
        this.dropdown.remove();
        this.selectElem.show();
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
        this._addClass(self.options.classes.isActive);
        $('body').on('click', function (event) {
            var $el = $(event.target);
            if (self.element.is($el) || self.element.has($el).length) {
                return false;
            } else {
                $('body').off(event);
                self.option('isActive', false);
            }
        });

        this._trigger('open');
    },

    /**
     * Закрыть меню
     * @event close
     * @public
     */
    close: function () {
        var self = this;

        this._removeClass(self.options.classes.isActive);
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
            $target = $li.eq(index),
            data = $target.data('value'),
            value = data.value;

        this._setData(data);
        this._trigger('select', null, value);

        // Генерируем событие change вручную. Необходимо для взаимодействия с другими фреймворками
        // (например, с Knockout.js). Если метод вызван событием changedOutside выше, то событие не генерируем
        // иначе будет бесконечная рекурсия.
        if (!outside) {
            this.selectElem.val(value).change();
        }
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

    /**
     * Обработчик клика по кнопке
     * @param event
     * @private
     */
    _handleButtonClick: function (event) {
        var isActive = this.option('isActive');
        this.option('isActive', !isActive);
        event.preventDefault();
    },

    /**
     * Обработчик клика по элементу из списка
     * @param event
     * @private
     */
    _handleItemClick: function (event) {
        var index = $(event.target).parent("li").index();
        this.select(index);
        event.preventDefault();
    },

    _setData: function (data) {
        this.hidden.val(data.value);
        this.selectText.text(data.text);
        this.placeholder.hide();
    },

    /**
     * Рендеринг меню
     * @private
     */
    _renderMenu: function () {
        var self = this,
            $options = this.selectElem.find('option'),
            selectIndex = $options.filter('[selected]').index(),
            $ul = $(this.list)
                .detach()
                .empty();

        $.each($options, function (index, item) {
            var isSelected = (index === selectIndex),
                $item = $(item);

            self._renderItemData($ul, $item, index, isSelected).appendTo($ul);
        });

        this.dropdown.children(".modal").append($ul);
        this.element.append(this.dropdown);

        if (selectIndex > -1) { this.select(selectIndex); }
    },
    /**
     * Рендеринг кнопки
     * @returns {jQuery} Возвращает jQuery обертку элемента <a>
     * @private
     */
    _renderButton: function () {
        var self = this,
            selectedOptionText = this.selectElem.find('option[selected]').text(),
            $button = $('<a/>', {'role': 'button', 'href': '#'}),
            $placeholder = $('<span/>', {'class': self.options.classes.selectPlaceholder}),
            $text = $('<span/>', {'class': self.options.classes.selectText});

        if (!selectedOptionText) {
            $placeholder
                .text(self.data.placeholderText)
                .appendTo($button);
        }

        $text
            .text(selectedOptionText)
            .appendTo($button);

        this._addClass($button, self.options.classes.selectButton);

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
        var self = this,
            $li = $('<li/>')
                .data('value', {
                    value: $item.val(),
                    text: $item.text(),
                    data: $item.data()
                });
        this._addClass($li, self.options.classes.selectListItem);
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

        $('<a>', {'href': '#'})
            .text(text)
            .appendTo($li);

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
        return this._super(key, val);
    }
});