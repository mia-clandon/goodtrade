import './ui-input-tips';

export default $.widget('ui.inputProduct', $.ui.inputTips, {
    options: {
        url: '/api/product/find',
        data: {
            query: '',
            limit: 2
        },
        paramName: 'product_id',
        hidden: null,
        tipsClass: 'tips-tiles'
    },
    _create: function () {
        var $root = this.element,
            $hidden = this.options.hidden = $root.find('input[type="hidden"]');
        if (!$hidden.length) {
            this.options.hidden = $('<input/>', {'type': 'hidden'}).prependTo($root);
        }
        return this._super();
    },
    _init: function () {
        var $root = this.element,
            $hidden = this.option('hidden'),
            paramName = this.option('paramName'),
            value = $hidden.val();
        if (!$hidden.attr('name')) {
            $hidden.attr('name', paramName);
        }
        if (value && $.isNumeric(value)) {
            this.option('id', value);
        }
        this._on($root, {
            'inputproductselect': this._handleSelect,
            'inputproductskip': this._handleSkip
        });
        return this._super();
    },
    _handleSelect: function (e, data) {
        this.option('id', data.id);
        this._setValue(data.title);
    },
    _handleSkip: function () {},
    _destroy: function () {
        var $root = this.element,
            $hidden = this.option('hidden');
        $hidden.remove();
        this._off($root, 'inputproductselect');
        this._off($root, 'inputproductskip');
        return this._super();
    },
    _setOption: function (key, val) {
        var $hidden = this.option('hidden'),
            $root = this.element;
        if (key == 'id') {
            $hidden.val(val);
        } else if (key == 'value') {
            $root.find('a[data-action="skip"]').data('value', {text: val});
        }
        return this._super(key, val);
    },
    _renderTips: function (data) {
        var className = this.option('tipsClass'),
            $list = $('<ul/>');
        this._addClass($list, className);
        $.each(data, function (index, obj) {
            var $li = $('<li/>').appendTo($list),
                $a = $('<a/>', {
                    'data-action': 'select',
                    'data-value': JSON.stringify(obj),
                    'text': obj.title,
                    'role': 'button',
                    'href': '#'
                }).appendTo($li);
            $('<img/>', {'src': obj.image, 'alt': obj.title}).prependTo($a);
            $('<small/>', {'text': (obj.category) ? obj.category : ''}).appendTo($a);
        });
        return $list;
    }
});