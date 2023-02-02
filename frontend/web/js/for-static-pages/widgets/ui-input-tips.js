define(['jquery','jquery-ui','widgets/ui-input'],function ($) {
   return $.widget('ui.inputTips', $.ui.input,{
       options : {
         timer : null,
         url : '',
         type : 'POST',
         data : null,
         tips : null,
         tipsBody : null,
         hasTips : false,
         open : false,
         delay: 300,
         template : '',
         model : {},
         tipsClass : 'tips-list',
         show : 'fadeIn',
         hide : 'fadeOut',
         duration : 150
       },
       _create : function() {
           var $root = this.element,
               $tips = this.options.tips = $('.tips', $root);
           this.options.tipsBody = $('.tips-body', $root);
           this._on($root, {'focus > input' : this._handleFocus});
           this._on($tips, {'click a[data-action]' : this._handleTipSelect});
           return this._super();
       },
       _init : function() {
           var $input = this.option('inputField');
           $input.attr('autocomplete','off');
           this.options.value =  $input.val();
           return this._super();
       },
       _destroy : function() {
          var $root = this.element,
              $tips = this.option('tips');
           this._off($root, 'focus');
           this._off($tips, 'click');
           return this._super();
       },
       refresh : function() {
            this.option('value', '');
           return this._super();
       },
       _handleTipSelect : function(event) {
             var $el = $(event.target),
                 action = $el.data('action'),
                 value = $el.data('value');
             if(event.which == 1) {
                 this._trigger(action, event, value);
                 this.option('open', false);
                 event.preventDefault();
             }
       },
       _setOption : function(key, val) {
           var $tips = this.option('tips'),
               $input = this.option('inputField'),
               hasTips = this.option('hasTips'),
               show = this.option('show'),
               hide = this.option('hide'),
               duration = this.option('duration'),
               $self = this;
           if(key == 'value') {
              if(val.length >= 3) {
                  this._getTips();
              } else {
                  this.option('hasTips', false);
              }
           } else if (key == 'open') {
               if(val && $input.is(':focus') && hasTips) {
                   $tips.animate({marginTop : 10},{duration : duration, queue : false});
                   this._show($tips, {effect : show, duration : duration});
                   $('body').on('click', function(e) {
                      var $el = $(e.target);
                       if($tips.has($el).length || $el.is($tips) || $el.is($input)) {
                           return false;
                       } else {
                           $('body').off(e);
                           $self.option('open', false);
                       }
                   });
               } else {
                   $tips.animate({marginTop : 15},{duration : duration, queue : false});
                   this._hide($tips, { effect : hide, duration : duration});
               }
           } else if(key=='hasTips') {
               if(val) {
                   this.option('open', true);
               } else {
                   this.option('open', false);
               }
           }
           return this._super(key, val);
       },
       _highlightText : function (text){
           var value = this.option('value'),
               regexp = new RegExp(value,'i'),
               start = text.search(regexp),
               end = start + value.length;
           return text.replace(regexp,'<strong>' +  text.substring(start,end) +  '</strong>');
       },
       _handleFocus : function (e) {
           this.option('open', true);
       },
       _setValue : function(value) {
           this.option('hasTips', false);
           return this._super(value);
       },
       _getTips : function() {
           var timer = this.option('timer'),
               delay = this.option('delay');
               clearTimeout(timer);
               this.option('timer',this._delay(this._ajax,delay));
       },
       _ajax : function() {
           var value = this.option('value'),
               url = this.options.url,
               type = this.options.type || 'POST',
               data = (this.options.data) ? $.extend(this.options.data,{query:value}) : {query:value},
               self = this;
           $.ajax({url: url , type: type, data: data}).done(function(response){
                self._handleTips(response);
           });
       },
       _handleTips : function(response) {
           var error = response.error,
               data = response.data;
           if(error == 0) {
               if(data.length) {
                   this._replaceTips(this._renderTips(data));
                   this.option({hasTips : true,
                                open : true});
               } else {
                   this.option('hasTips', false);
               }
           } else if(error == 1 ) {
               //$.error('Sends the string is empty!');
           } else if(error == 2) {
               $.error('Internal Error!');
           }
       },
       _replaceTips : function (html) {
           var $tipsBody = this.option('tipsBody'),
               $tips = this.option('tips');
               $tipsBody.detach()
                   .empty()
                   .append(html)
                   .prependTo($tips);
       }
   });
});