define(['jquery','jquery-ui', 'jquery.cookie','widgets/ui-action'], function($) {
    return $.widget('ui.counter', {
        options : {
            key : ''
        },
        _create : function() {
            var $root = this.element;
            this.options.key = $root.data('key');
            $.cookie.json = true;
        },
        _init : function() {
            var key = this.option('key'),
                cookie = $.cookie(key);
            if(cookie && $.type(cookie) == 'Array'){
                this.option('count', cookie.length);
            }
        },
        _setOption : function(key, val) {
            if(key == 'count') {
                if(val) {
                    this._addClass('is-active');
                } else {
                    this._removeClass('is-active');
                }
            }
            this._super(key, val);
        }
    });
});