define(['jquery', 'jquery-ui'], function($){
   return $.widget('ui.printer', {
      options : {
         printCss : '../public/css/print.css',
         iframe : null
      },
      _create : function() {
         var $button = $('#print-btn');
         this.options.iframe = $('<iframe/>');
         this._on($button, {'click' : this._handleClick});
      },
      _init : function() {
         var $root = this.element,
             stylesheet = $root.data('stylesheet'),
             $iframe = this.option('iframe'),
             $body = $('body');
         $body.append($iframe);
         if(stylesheet) {
            this.option('stylesheet', stylesheet);
         }
      },
      _destroy : function() {
         var $button = $('#print-btn');
         this._off($button, 'click');
      },
      print : function() {
         var $root = this.element,
             $clone = $root.clone(),
             $iframe = this.option('iframe'),
             $iframeContent = $iframe.contents(),
             $iframeBody = $iframeContent.find('body');
         $iframeBody
             .empty()
             .append($clone);
         console.log($iframeBody);
      },
      _setOption : function(key, val) {
         var $iframe = this.option('iframe'),
             $head = $iframe.contents();
         if(key == 'printCss') {

         }
         return this._super(key, val);
      },
      _handleClick : function(e) {
         this.print();
         e.preventDefault();
      }
   });
});