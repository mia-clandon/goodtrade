define('widgets/ui-tags-product', ['jquery', 'jquery-ui', 'widgets/ui-tags-category'], function ($) {
    $.widget('ui.tagsProduct', $.ui.tagsCategory, {
        options: {
            paramName: 'product[]',
            tagsLimit: 3
        },
        _renderTag: function (item) {
            var $tag = this._super(item),
                $input = $tag.children('input:hidden'),
                value;
            if ('children' in item) {
                value = item.children.value;
                $.merge(this._renderDelimeter(), this._renderLabel(item.children.label)).insertBefore($input);
                if ('children' in item.children) {
                    $.merge(this._renderDelimeter(), this._renderLabel(item.children.children.label)).insertBefore($input);
                    value = item.children.children.value;
                }
            }
            $input.val(value);
            return $tag;
        },
        _renderLabel: function (label) {
            var $label = $('<span/>', {'text': label});
            this._addClass($label, 'ui-tags-item-label');
            return $label;
        },
        _renderDelimeter: function () {
            return $('<i/>', {'class': 'icon icon-delimeter'});
        }
    });
});