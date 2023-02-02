define(['jquery','jquery-ui',],function($) {
  return  $.widget("ui.dropdown", {
        options: {
            value: '',
            active: false
        },
        _create: function () {
            this._on(this.element, {click: '_handleClick'});
            this._on(this.element.find('.dropdown-menu-item'), {click: '_handleItemClick'});
        },
        _setOption: function (key, val) {
            if (key == 'active') {
                if (val) {
                    this.element.addClass('is-active');
                } else {
                    this.element.removeClass('is-active');
                }
            }
            if (key == 'value') {
                this.option('active', false);
            }

            this._super(key, val);
        },
        _handleClick: function () {
            var self = this;
            if (!this.option('active')) {
                this.option('active', true);
            } else {
                this.option('active', false);
            }
            $(document.body).one('click', function () {
                self.option('active', false);
                return false;
            });
            return false;
        },
        _handleItemClick: function (e) {
            this.option('value', $(e.target).text());
            return false;
        }
    });
});