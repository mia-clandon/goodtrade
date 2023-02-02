import './ui-autocomplete-patch';
import './ui-tags';

/**
 * Виджет характеристик
 * @example Пример использования
 * // $('.input-hash').inputHash();
 * @version 1.0.0
 * @exports widgets/ui-input-hash-old
 * TODO : 1. Сделать фикс с параметрами (при удалении индексы дублируются)
 */
export default $.widget('ui.inputHashOld', [$.ui.formResetMixin, {
        options: {
            rowCount: 0,
            paramName: 'tech-specs',
            hide: 'slideUp',
            show: 'slideDown',
            autocomplete: {
                source: '/api/vocabulary/find'
            }
        },
        _create: function () {
            //noinspection JSUnresolvedVariable
            var $table = this.element.children('.input-hash-table'),
                $button = this.element.children('.input-hash-add'),
                $input = this.element.children('.input-hash-field');

            // Проверка наличия кнопки
            if ($button.length) {
                this.button = $button;
            } else {
                //noinspection JSUnresolvedVariable
                this.button = this._renderAddButton().appendTo(this.element);
            }

            // Проверка наличия инпута
            if ($input.length) {
                this.input = $input;
            }
            else {
                //noinspection JSUnresolvedVariable
                this.input = this._renderInput().prependTo(this.element);
            }
            // Инициализация виджета autocomplete
            this.input.autocomplete(this.options.autocomplete);

            // Проверка наличия таблицы
            if ($table.length) {
                this.table = $table;
                this.initialRows = $table
                    .children('.input-hash-table-row')
                    .detach();
                this._initRows();
            }
            else {
                //noinspection JSUnresolvedVariable
                this.table = this._renderTable().prependTo(this.element).hide();
            }
            // Для привязки классов jQuery UI CSS Framework
            this._enhance();
            //noinspection JSUnresolvedFunction
            this._on(this.button, {'click': '_handleAddButtonClick'});
            //noinspection JSUnresolvedFunction
            this._on(this.input, {
                'autocompleteselect': '_handleSelect',
                'keyup': '_handleKeyUp'
            });
            //noinspection JSUnresolvedFunction
            this._on(
                {
                    'click .input-hash-table-cell-close': '_handleCloseButtonClick',
                    'tagsremovetag .input-hash-table-cell-key': '_handleRemoveTag',
                    'tagsaddtag .input-hash-table-cell-key': '_handleAddTag'
                });
            this.uniqueIndex = 0;
        },

        refresh: function () {
            this.table.empty();
            this._initRows(this.initialRows);
        },
        _initRows: function (rows) {
        },
        /**
         * Привязка классов jQuery UI CSS Framework
         * @private
         */
        _enhance: function () {
            //noinspection JSUnresolvedFunction
            this._addClass('ui-widget', 'ui-front');
            //noinspection JSUnresolvedFunction
            this._addClass(this.table, 'ui-widget-content');
        },
        /**
         * Добавление элемента в таблицу
         * @param {Object} item
         */
        addRow: function (item) {
            var $row = this._renderRow().appendTo(this.table),
                unique_index = this.uniqueIndex,
                $tags = $row.find('.ui-tags');
            $tags.first().tags({
                isEditable: true,
                tagsLimit: 1,
                paramName: 'tech_specs_key[' + unique_index + ']',
                autocomplete: {
                    source: '/api/vocabulary/find'
                }
            }).tags('addTag', item);
            $tags.last().tags({
                isEditable: true,
                paramName: 'tech_specs_value[' + unique_index + '][]',
                tagsLimit: 3,
                autocomplete: {
                    minLength: 0
                }
            });
            //noinspection JSUnresolvedFunction
            this.option('rowCount', ++this.options.rowCount);
            //noinspection JSUnresolvedFunction
            this._show($row, this.options.show);
            //noinspection JSUnresolvedFunction
            this._trigger('addRow', null, {item: item});
        },
        _removeRow: function (row) {
            var self = this;
            //noinspection JSUnresolvedFunction
            this._hide(row, this.options.hide, function () {
                $(this).remove();
                self.option('rowCount', --self.options.rowCount);
            });
        },
        removeRowByIndex: function (index) {
            this._getRowByIndex(index).remove();
        },
        _getRowByIndex: function (index) {
            return this.table.children('.input-hash-table-row:eq(' + index + ')');
        },
        _getKeyCellByIndex: function (index) {
            return this._getRowByIndex(index).children('.input-hash-table-cell-key');
        },
        _getValueCellByIndex: function (index) {
            return this._getRowByIndex(index).children('.input-hash-table-cell-value');
        },
        _handleCloseButtonClick: function (event) {
            var $el = $(event.target),
                $row = $el.closest('.input-hash-table-row');
            this._removeRow($row);
            event.preventDefault();
        },
        _handleAddButtonClick: function (event) {
            var value = this.input.val();
            this.addRow({label: value, value: value});
            event.preventDefault();
        },
        _handleSelect: function (event, ui) {
            this.input.autocomplete('close').val('');
            this.addRow(ui.item);
            event.preventDefault();
        },
        _handleKeyUp: function (event) {
            var $el = $(event.target),
                value = $el.val();
            if (event.keyCode === $.ui.keyCode.ENTER) {
                this.addRow({label: value, value: value});
            }
        },
        _handleAddTag: function (event, ui) {
            var $el = $(event.target),
                $valueTags = $el.parent()
                    .next()
                    .children('.ui-tags');
            if ($.isNumeric(ui.value) && ui.value > 0) {
                $.ajax({
                    url: '/api/vocabulary/terms',
                    type: 'POST',
                    data: {
                        vocabulary_id: ui.value
                    }
                })
                .done(function (data) {
                    $valueTags.tags('option', 'autocomplete', {source: data.data});
                });
            }
        },
        _handleRemoveTag: function (event, ui) {
            var $el = $(event.target),
                $row = $el.closest('.input-hash-table-row');
            this._removeRow($row);
        },
        _renderRow: function () {
            var $row = $('<li/>').hide(),
                $valueCell = this._renderValueCell(),
                $keyCell = this._renderKeyCell();
            //noinspection JSUnresolvedFunction
            this._addClass($row, 'input-hash-table-row');
            return $row.append($keyCell, $valueCell);
        },
        _renderKeyCell: function () {
            var $cell = this._renderCell(),
                $close = this._renderCloseButton();
            //noinspection JSUnresolvedFunction
            this._addClass($cell, 'input-hash-table-cell-key');
            return $cell.append($close);
        },
        _renderValueCell: function () {
            var $cell = this._renderCell();
            //noinspection JSUnresolvedFunction
            this._addClass($cell, 'input-hash-table-cell-value');
            return $cell;
        },
        _renderCell: function () {
            var $cell = $('<div/>'),
                $wrap = $('<div/>');
            //noinspection JSUnresolvedFunction
            this._addClass($wrap, 'input-hash-table-cell');
            //noinspection JSUnresolvedFunction
            this._addClass($cell, 'ui-tags');
            return $wrap.append($cell);
        },
        _renderInput: function () {
            var $input = $('<input/>', {'type': 'text'});
            //noinspection JSUnresolvedFunction
            this._addClass($input, 'input-hash-field');
            return $input;
        },
        _renderAddButton: function () {
            var $button = $('<a/>', {
                    'role': 'button',
                    'href': '#'
                }),
                $icon = $('<i/>', {
                    'class': 'icon icon-plus'
                });
            //noinspection JSUnresolvedFunction
            this._addClass($button, 'input-hash-button');
            return $button.append($icon);
        },
        _renderCloseButton: function () {
            var $closeButton = $('<a/>', {
                    'role': 'button',
                    'href': '#'
                }),
                $icon = $('<i/>', {'class': 'icon icon-close-lg'});
            //noinspection JSUnresolvedFunction
            this._addClass($closeButton, 'input-hash-table-cell-close');
            return $closeButton.append($icon);
        },
        _renderTable: function () {
            var $table = $('<ul/>');
            //noinspection JSUnresolvedFunction
            this._addClass($table, 'input-hash-table');
            return $table;
        },
        _setOption: function (key, val) {
            if (key === 'rowCount') {
                this.uniqueIndex++;
                if (val) {
                    this.table.show();
                }
                else {
                    this.table.hide();
                }
            }
            //noinspection JSUnresolvedFunction
            return this._super(key, val);
        }
    }]
);