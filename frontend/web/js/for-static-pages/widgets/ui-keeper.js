define('widgets/ui-keeper', ['jquery','jquery-ui', 'jquery.cookie'], function($) {
    var keeperWidget = $.widget('ui.keeper', {
        options : {
            id : 0,
            active : false,
            key : '',
            options : {
                expires : 7
            }
        },
        _create : function() {
            var $root = this.element;
            this.options.key = $root.data('key');
            this.options.id = $root.data('id');
            this._on($root, {'click' : this._handleClick});
            $.cookie.json = true;
        },
        _init : function() {
            var key = this.option('key'),
            id = this.option('id'),
            cookie = $.cookie(key) || [],
            $counter = $('#'+key);
            if($.inArray(id, cookie) != -1) {
            this.option('active', true);
            }
            if(cookie.length) {
            $counter.show().text(cookie.length);
            } else {
            $counter.hide();
            }
        },
        _destroy : function() {
            var $root = this.element;
            $root._off('click');
        },
        _handleClick : function(e) {
            var id = this.option('id'),
            key = this.option('key');
            this.set(key, id);
            e.preventDefault();
        },
        set : function(key, id ) {
            var $counter = $('#' + key),
            cookie = $.cookie(key) || [],
            options = this.option('options');
            if($.inArray(id, cookie) != -1) {
            cookie.splice(cookie.indexOf(id), 1);
            this.option('active', false);
            } else {
            cookie.push(id);
            this.option('active', true);
            }
            if(cookie.length) {
            $counter.show().text(cookie.length);
            } else {
            $counter.hide();
            }
            $.cookie(key, cookie, options);
        },
        _setOption : function(key, val) {
        var cookieKey = this.option('key'),
        id = this.option('id'),
        $instances = $(':data("ui-keeper")')
        .filter('[data-key="'+cookieKey+'"]')
        .filter('[data-id="'+id+'"]');
        if(key == 'active') {
        if(val) {
        this._addClass($instances, 'is-active');
        } else {
        this._removeClass($instances, 'is-active');
        }
        }
        this._super(key, val);
        }
        });
        return keeperWidget;
});