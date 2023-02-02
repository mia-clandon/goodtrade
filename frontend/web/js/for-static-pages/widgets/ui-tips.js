define(['jquery','jquery-ui'],function($){
   return $.widget('ui.tips', {
       options : {
          body : null,
          open : false,
          show : 'fadeIn',
          hide : 'fadeOut',
          duration : 150
       },
       _create : function() {
           var $root = this.element;
           this.options.body = $root.find('.tips-body');
       },
       show : function() {
           this.option('open', true);
       },
       hide : function() {
           this.option('open', false);
       },
       replace : function(html) {
           var $root = this.element,
               $body = this.option('body');
           $body.detach()
               .empty()
               .append(html)
               .prependTo($root);
       },
       _setOption : function(key, val) {
           var $root = this.element,
               $self = this,
               show = this.option('show'),
               hide = this.option('hide'),
               duration = this.option('duration');
           if(key == 'open') {
               if(val) {
                   $('body').on('click', function(e){
                       var $el = $(e.target),
                           $parent = $root.parent();
                       if($parent.is($el) || $parent.has($el).length)  {
                           return false;
                       } else {
                           $self.option('open', false);
                           $('body').off(e);
                       }
                   });
                   $root.animate({marginTop : 10},{duration : duration, queue : false});
                   this._show($root, {effect : show, duration : duration});
                   this._trigger('show');
               } else {
                   $root.animate({marginTop : 15},{duration : duration, queue : false});
                   this._hide($root, { effect : hide, duration : duration});
                   this._trigger('hide');
               }
           }
           return this._super(key, val);
       }
   });
});