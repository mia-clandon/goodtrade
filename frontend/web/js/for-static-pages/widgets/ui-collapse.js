define(['jquery','jquery-ui'], function($) {
   return $.widget('ui.collapse', {
       options: {
           open: false,
           content: null,
           button: null,
           start: 0,
           end: 0,
           duration: 200,
           startContentHeight : 0
       },
       _create: function () {
           var $root = this.element,
               $button = this.options.button = $root.find('.js-collapse-toggle');
           this.options.content = $root.find('.js-collapse-content');
           this._on($button, {'click': this._handleClick});
       },
       _init: function () {
           var $root = this.element,
               $content = this.option('content');
           if ($root.hasClass('js-collapse-open') || $root.data('open')) {
               this.option('open', true);
           }
           $content.height(this.options.startContentHeight);
           this.options.start = $content.height();
           this.options.end = $content.prop('scrollHeight');
       },
       _destroy: function () {
           var $button = this.option('button');
           this._off($button, 'click');
       },
       _handleClick: function (e) {
           var currentState = this.option('open');
           this.option('open', !currentState);
           e.preventDefault();
       },
       _setOption: function (key, val) {
           var $root = this.element,
               $content = this.option('content'),
               start = this.option('start'),
               end = this.option('end') || $content.prop('scrollHeight'),
               duration = this.option('duration'),
               self = this;
           if (key == 'open') {
               if (val) {
                   $content.animate({height: end}, {
                       duration: duration,
                       easing: 'linear',
                       complete: function () {
                           self._trigger('showend');
                           $(this).attr("style", "");
                       }
                   });
                   this._addClass($root, 'is-open');
                   this._trigger('show');
               }
               else {
                   $content.animate({height: start}, {
                       duration: duration,
                       easing: 'linear',
                       complete: function () {
                           self._trigger('hideend');
                       }
                   });
                   this._removeClass($root, 'is-open');
                   this._trigger('hide');
               }
           }
           return this._super(key, val);
       }
   });
});