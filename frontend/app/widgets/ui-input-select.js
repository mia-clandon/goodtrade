import './ui-input';

export default $.widget("ui.inputSelect", $.ui.input, {
    options: {
        list: null,
        items: null,
        active: false
    },
    _create: function () {
        this.options.list = this.element.find('ul');
        this.options.items = this.options.list.find('li');
        this._on(this.element, {click: this._handleClick});
        this._on(this.element, {'change input': this._handleChange});
        return this._super();
    },
    _handleKeyUp: function (e) {
        this.option('active', false);
        return this._super(e);
    },
    _handleChange: function (e) {
        var $items = this.option('items');
        $items.removeClass('is-selected');
    },
    _handleClick: function (e) {
        var $el = $(e.target),
            $tagName = $el.prop('tagName'),
            $items = this.option('items'),
            value = '';
        if ($tagName == 'LI') {
            value = $el.text();
            $items.removeClass('is-selected')
                .eq($el.index())
                .addClass('is-selected');
            this.option('active', false);
            this._setValue(value);
            this._trigger('select', null, {value: value, target: $el});
        }
        else {
            this.option('active', true);
        }
        return false;
    },
    _setValue: function (value) {
        return this._super(value);
    },
    _setOption: function (key, val) {
        var self = this;
        if (key == 'active') {
            if (val) {
                this.element.addClass('is-active');
                $(document.body).one('click', function (e) {
                    self.option('active', false);
                });
            }
            else {
                this.element.removeClass('is-active');
            }
        }
        return this._super(key, val);
    }
});