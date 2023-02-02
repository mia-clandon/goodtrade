import './ui-tags';

export default $.widget('ui.tagsCategory', $.ui.tags, {
    options: {
        paramName: '',
        tagsLimit: 3
    },
    _renderTag: function (item) {
        var $control = $('input.activity-input');
        if ($control.length > 0) {
            this.options.paramName = $control.attr('name');
            $control.remove();
        }
        var icon_class = $('li[data-value='+item.value+']').data('icon'),
            $tag = this._super(item),
            $icon = $('<i/>', {
                'class': 'icon ' + icon_class
            });
        return $tag.prepend($icon);
    }
});