export default $.widget('ui.deliveryCondition', {
    options: {
        items: [],
        input: null,
        list: null
    },
    _create: function () {
        this.options.input = this.element.find('input:hidden');
        this.options.list = this.element.find('ul');
        this._on(this.element, {'click li': this._handleSelect});
    },
    _init: function () {
        var $input = this.option('input'),
            $list = this.option('list'),
            value = $input.val() || '[]',
            array = JSON.parse(value),
            $listItems = $list.find('li');
        $.each($listItems, function (i, obj) {
            var $item = $(obj);
            if ($item.data('value') in array) {
                $item.addClass('is-selected');
            }
        });
        this.option('items', array);
    },
    _handleSelect: function (e) {
        var $item = $(e.target),
            $value = $item.data('value');
        if ($item.hasClass('is-selected')) {
            $item.removeClass('is-selected');
            this._removeItem($value);
        } else {
            $item.addClass('is-selected');
            this._addItem($value);
        }
    },
    _addItem: function (value) {
        var items = this.option('items');
        items.push(value);
        this.option('items', items);
    },
    _removeItem: function (value) {
        var items = this.option('items'),
            index = $.inArray(value, items);
        items.splice(index, 1);
        this.option('items', items);
    },
    _setOption: function (key, val) {
        var $input = this.option('input');
        if (key == 'items') {
            $input.val(JSON.stringify(val));
        }
        return this._super(key, val);
    }
});