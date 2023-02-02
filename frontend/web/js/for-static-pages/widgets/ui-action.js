define(['jquery','jquery-ui', 'jquery.cookie','widgets/ui-counter'], function($) {
   return $.widget('ui.action', {
      options : {
        id : 0,
        active : false,
        key : '',
        options : {
            expires : 7
        },
        counter : null
      },
       _create : function() {
          var $root = this.element;
           this.options.key = $root.data('key');
          this.options.id = $root.data('id');
          this.options.counter = $(':data("ui-counter")')
              .filter('[data-key="' + $root.data('key') +'"]');
          this._on($root, {'click' : this._handleClick});
           $.cookie.json = true;
      },
    _init : function() {
         var key = this.option('key'),
             id = this.option('id'),
             cookie = $.cookie(key);
         if(cookie && id in cookie){
             this.option('active', true);
         }
    },
    _handleClick : function(e) {
          var active = this.option('active'),
              id = this.option('id'),
              key = this.option('key'),
              options = this.option('options'),
              cookie = $.cookie('key') || [],
              $counter = this.option('counter'),
              $instances =  $(':data("ui-action")')
                  .filter('[data-key="' + key + '"]')
                  .filter('[data-id="' + id + '"]');
          $instances.action('option','active', !active);
          if(id in cookie) {
              cookie.splice(cookie.indexOf(id),1);
          } else {
              cookie.push(id);
          }
          $.cookie(key, cookie, options);
          e.preventDefault();
    },
     _setOption : function(key, val) {
        if(key == 'active') {
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