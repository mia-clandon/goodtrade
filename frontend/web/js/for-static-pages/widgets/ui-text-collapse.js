define(['jquery','jquery-ui'],function($){
   return $.widget('ui.textCollapse',{
            options : {
                content : null,
                button: null,
                open : false
            },
            _create : function() {
                $button = this.options.button = this.element.find('.text-collapse-btn');
                this.options.content = this.element.find('.text-collapse-content');
                this._on($button,{'click':this._handleButtonClick});
            },
            _init : function() {
               if(this.element.hasClass('is-open')) {
                   this.option('open',true);
               }
            },
            _handleButtonClick : function(e) {
                var $self = $(this),
                    $button = this.option('button'),
                    $content = this.option('content'),
                    scrollHeight = $content.prop('scrollHeight');
                $button.detach();
                if(Modernizr.csstransitions) {
                    $content.css({maxHeight:scrollHeight+'px'});
                    this.element.addClass('is-open');
                } else {
                    $content.animate({maxHeight: scrollHeight+'px'},1000,function(){
                        $self.element.addClass('is-open');
                    });
                }
            }
   });
});