define(['jquery', 'jquery-ui', 'jquery.perfectScrollbar'], function($){
    return $.widget('ui.settings', {
        options: {
          open: false
        },
        _create: function () {
           let $root = this.element,
               $close = $root.find('#settings-close'),
               $open = $('#settings-open');

           this._on($close, {'click': this._handleClick});
           this._on($open, {'click': this._handleClick});
        },
        _init: function () {
           let $root = this.element,
               $body = $root.children('.settings-body');

           $root.perfectScrollbar();
        },
        send: function () {},
        _destroy: function () {
           let $root = this.element,
               $open = $('#settings-open'),
               $close = $root.find('#settings-close');
           this._off($close, 'click');
           this._off($open, 'click');
        },
        _handleClick: function (e) {
             let currentState = this.option('open');
             this.option('open', !currentState);
             e.preventDefault();
        },
        _setOption: function (key, val) {
            let $root = this.element,
                $open = $('#settings-open'),
                $page = $('#page-wrap'),
                $topControls = $('#top-controls'),
                $bottomControls = $('#bottom-controls');

            if (key === 'open') {
                if (val) {
                    this._addClass($page, 'is-shifted-right-more');
                    this._addClass($open, 'opened-settings');
                    this._addClass($topControls, 'is-shifted-right-more');
                    this._addClass($bottomControls, 'is-shifted-right-more');
                    this._addClass($root, 'is-open');
                } else {
                    this._removeClass($page, 'is-shifted-right-more');
                    this._removeClass($open, 'opened-settings');
                    this._removeClass($topControls, 'is-shifted-right-more');
                    this._removeClass($bottomControls, 'is-shifted-right-more');
                    this._removeClass($root, 'is-open');
                }
            }
            return this._super(key, val);
        }
    });
});