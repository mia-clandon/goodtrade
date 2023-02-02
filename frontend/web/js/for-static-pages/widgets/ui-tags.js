// TODO : Добавить ARIA разметку
define('widgets/ui-tags', ['jquery', 'jquery-ui', 'widgets/ui-autocomplete-patch'], function ($) {
    /**
     * Виджет тэгов
     * @example Пример использования виджета
     * // $('.ui-tags').tags();
     * @version 1.0.0
     * @author Kenzhegulov Madiyar
     */
    $.widget('ui.tags', [$.ui.formResetMixin, {
        version: '1.0.0',
        options: {
            isEditable: false,
            paramName: '',
            removeConfirmation: false,
            allowDuplicate: false,
            allowSpaces: true,
            placeholderText: '',
            maxLength: 24,
            tagsLimit: 3,
            tagsCount: 0,
            show: 'fadeIn',
            hide: 'fadeOut',
            autocomplete: {
                source: []
            }
        },

        _create: function () {
            // Инициализируем кэш тэгов
            this.tagsCache = [];
            this._addClass('ui-widget');
        },
        /**
         * Инициализация виджета
         * @private
         */
        _init: function () {
            var $tags = this.element.children('.ui-tags-item');
            // Если имеются теги необходимо их закешировать и инициализировать
            if ($tags.length !== 0) {
                this.initialTags = $tags.detach();
                this._parseTags($tags);
            }

            if (this.options.isEditable === true) {
                var $input = this.input = this._renderInput()
                    .appendTo(this.element);
                this._addClass('ui-tags-editable');
                this._on({
                    'click': '_handleClick',
                    'keyup .ui-tags-input-field': '_handleKeyUp',
                    'keydown .ui-tags-input-field': '_handleKeyDown',
                    'keypress .ui-tags-input-field': '_handleKeyPress',
                    'change .ui-tags-input-field': '_handleChange',
                    'autocompleteselect': '_handleSelect',
                    'autocompleteresponse': '_handleResponse'
                });
                this.option('autocomplete', {appendTo: $input});
            }

            this._on({
                'click .ui-tags-item-close': '_handleCloseClick'
            });
            this._bindFormResetHandler();
        },
        /**
         * Деструктор виджета
         * @private
         */
        _destroy: function () {
            if (this.options.isEditable === true) {
                this._off(this.element, 'change');
                this._off(this.element, 'keyup');
                this._off(this.element, 'keydown');
                this._off(this.element, 'focus');
                this._off(this.element, 'click');
            }
            this._unbindFormResetHandler();
        },
        /**
         * Обновить состояние виджета
         * @event refresh
         */
        refresh: function () {
            this.clear();
            //noinspection JSUnresolvedVariable
            this._parseTags(this.initialTags);
            //noinspection JSUnresolvedFunction
            this._trigger('refresh');
        },
        /**
         * Метод добавления тэга
         * @public
         */
        addTag: function (item) {

            // Если нету названия или пустая строка
            if ('label' in item === false || $.trim(item.label).length === 0) {
                return;
            }

            // Если редактируемый
            if (this.options.isEditable === true) {
                this.inputField.val('').blur();
                this.inputField.autocomplete('close');
            }

            // Если превышен лимит
            if (this.options.tagsCount === this.options.tagsLimit) {
                return;
            }
            // Если не допускается повторении и такой тэг уже имеется
            if (this.options.allowDuplicate === false && this.indexOfTag(item.label) > -1) {
                return;
            }
            // Если не допускаются пробелы
            if (this.options.allowSpaces === false && item.label.split(' ').length > 1) {
                return;
            }

            // Трим пробелов по краям
            item.label = this._trimEdges(item.label);

            if ('value' in item === false) {
                item.value = item.label;
            }

            // Если на событие вызвали event.preventDefault();
            if (this._trigger('addTag', null, item) === false) {
                return;
            }

            var $tag = this._renderTagData(item).hide();


            if (this.options.isEditable === true) {
                $tag.insertBefore(this.input);
            } else {
                $tag.appendTo(this.element);
            }
            this._show($tag, this.options.show);
            this.option('tagsCount', ++this.options.tagsCount);
        },
        /**
         * Очистка тэгов
         */
        clear: function () {
            var $tags = this.element.children('.ui-tags-item'),
                self = this;
            this.input.children('.ui-tags-input-field').val('');
            $.each($tags, function (i, tag) {
                self.removeTag($(tag));
            });
        },
        /**
         * Удалить тэг
         * @param {jQuery} tag
         * @public
         */
        removeTag: function (tag) {
            // Если это не экземпляр jQuery
            if (tag instanceof jQuery !== true || tag.length === 0) {
                return;
            }

            this._hide(tag, this.options.hide, function () {
                $(this).remove();
            });
            this._trigger('removeTag', null, tag.data('ui-tags-item'));
            this.option('tagsCount', this.options.tagsCount - 1);
        },
        /**
         * Удалить тэг по индексу
         * @param {Number} index
         * @public
         */
        removeTagByIndex: function (index) {
            if (typeof index !== 'number') {
                return;
            }
            var $tag = this.element
                .children('.ui-tags-item')
                .eq(index);
            this.removeTag($tag);
        },
        /**
         * Удалить тэг по названию
         * @param {String} label
         * @public
         */
        removeTagByLabel: function (label) {
            if (typeof label !== 'string') {
                return;
            }
            var $tags = this.element.children('.ui-tags-item'),
                $targetTag = $tags.filter(':contains(' + label + ')');
            this.removeTag($targetTag);
        },
        /**
         * Удалить тэг по значению
         * @param {String} value
         * @public
         */
        removeTagByValue: function (value) {
            if (typeof value !== 'string') {
                return;
            }
            var $tags = this.element.children('.ui-tags-item'),
                $targetTag = $tags.has('.ui-tags-item-value [value="' + value + '"]');
            this.removeTag($targetTag);
        },
        /**
         * Поиск индекса тэга по названию или значению
         * @param {String} label
         * @param {String} value
         * @returns {Number}
         * @public
         */
        indexOfTag: function (label, value) {
            var $tags = this.element.children('.ui-tags-item');
            //Фильтрация по названию
            if (typeof label === 'string') {
                $tags = $tags.filter(function (i, el) {
                    var $el = $(el),
                        $label = $el.children('.ui-tags-item-label');
                    return $label.text() === label;
                });
            }
            // Фильтрация по значению
            if (typeof value === 'string') {
                $tags = $tags.filter(function (i, el) {
                    var $el = $(el),
                        $value = $el.children('.ui-tags-item-value');
                    return $value.val() === value;
                });
            }
            // Если тэг не найден
            if ($tags.length === 0) {
                return -1;
            }
            return $tags.index();
        },
        /**
         * Возвращает элемент виджета
         * @returns {jQuery}
         */
        widget: function () {
            return this.element;
        },
        // Трим пробелов по краям
        _trimEdges: function (string) {
            return string.replace(/^\s+/, '').replace(/\s+$/, '');
        },
        // Рэндеринг данных тэга
        _renderTagData: function (item) {
            return this._renderTag(item).data('ui-tags-item', item);
        },
        /**
         * Рендеринг тэга
         * @returns {jQuery}
         * @private
         */
        _renderTag: function (item) {
            var data = {};
            // выбрали с автозаполнения.
            if (item.hasOwnProperty('from_autocomplete') && item.from_autocomplete==true) {
                data = {id: item.value, name: item.label};
            }
            else {
                // ввели сами.
                data = {id: 0, name: item.label, value: item.value};
            }
            var $tag = $('<span/>'),
                $label = $('<span/>', {'text': item.label}),
                $value = $('<input/>', {
                    'type': 'hidden',
                    'value': JSON.stringify(data),
                    'name': this.options.paramName
                }),
                $close = $('<a/>', {'role': 'button', 'href': '#'});

            $('<i/>', {'class': 'icon icon-close'}).appendTo($close);

            this._addClass($tag, 'ui-tags-item');
            this._addClass($label, 'ui-tags-item-label');
            this._addClass($value, 'ui-tags-item-value');
            this._addClass($close, 'ui-tags-item-close');

            return $tag.append($label, $value, $close);
        },
        /**
         * Рендеринг инпута
         * @returns {jQuery}
         * @private
         */
        _renderInput: function () {
            var $wrap = $('<span/>'),
                $input = this.inputField = $('<input/>', {
                    'type': 'text',
                    'maxlength': this.options.maxLength,
                    'placeholder': this.options.placeholderText
                });
            $input.autocomplete(this.options.autocomplete);
            this.buffer = $('<span/>').css({
                fontSize: '14px',
                letterSpacing: '0.05em',
                display: 'none'
            }).appendTo($wrap);
            this._addClass($wrap, 'ui-tags-input', 'ui-front');
            this._addClass($input, 'ui-tags-input-field');
            return $wrap.append($input);
        },
        _inputWidth: function () {
            if (this.options.isEditable === false) {
                return;
            }
            var width = this.buffer.text(this.inputField.val()).width();
            this.inputField.width(width + 14);
        },
        _handleClick: function (event) {
            if (this.element.is(event.target)) {
                this.inputField.focus();
            }
        },
        _handleCloseClick: function (event) {
            var $button = $(event.currentTarget),
                $tag = $button.parent('.ui-tags-item');
            this.removeTag($tag);
            event.preventDefault();
        },
        _handleChange: function (event) {
            var $el = $(event.target),
                value = $el.val(),
                grep = null;
            if (this.tagsCache && this.tagsCache instanceof Array && this.tagsCache.length > 0) {
                grep = $.grep(this.tagsCache, function (tag) {
                    return tag.label.toLowerCase() === value.toLowerCase();
                });
            }
            if (grep && grep instanceof Array && grep.length === 1) {
                this.addTag(grep[0]);
            } else {
                this.addTag({label: value});
            }
        },
        _handleKeyDown: function (event) {
            var $el = $(event.target);
            $el.data('previousValue', $el.val());
        },
        _handleKeyPress: function () {
            this._inputWidth();
        },
        _handleResponse: function (event, ui) {
            this.tagsCache = ui.content;
        },
        _handleKeyUp: function (event) {
            var $el = $(event.target),
                previousValue = $el.data('previousValue'),
                currentValue = $el.val();
            if ($.ui.keyCode.BACKSPACE === event.keyCode && previousValue === '' && currentValue === '') {
                if (this.options.removeConfirmation === true) {
                    if ($el.data('removeConfirmation') === true) {
                        this.removeTagByIndex(-1);
                        $el.data('removeConfirmation', false);
                    } else {
                        $el.data('removeConfirmation', true);
                    }
                } else {
                    this.removeTagByIndex(-1);
                }
            }
        },
        _handleSelect : function(event, ui) {
             this.addTag( ui.item );
        },
        /**
         * Парсит тэги из jQuery коллекции
         * @param {jQuery} tags
         * @private
         */
        _parseTags: function (tags) {
            var self = this;
            $.each(tags, function (i, tag) {
                self.addTag($(tag).data('ui-tags-item'));
            });
        },
        _setOption: function (key, val) {
            if (key === 'tagsCount') {
                if (val === this.options.tagsLimit) {
                    if (this.options.isEditable === true) {
                        this.input.hide();
                    }
                    this._trigger('limitExceed', null, {tagsLimit: val});
                } else {
                    if (this.options.isEditable === true) {
                        this.input.show();
                    }
                }
                this._inputWidth();
            }
            if (key === 'disabled') {
                if (this.options.isEditable === true) {
                    this.inputField.prop('disabled', val);
                }
            }
            if (key === 'autocomplete') {
                this.inputField.autocomplete('option', val);
            }
            return this._super(key, val);
        }
    }]);
});