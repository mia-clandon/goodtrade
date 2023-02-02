define(['jquery', 'jquery-ui'], function() {
   return $.widget('ui.goodSelect', $.ui.selectmenu, {
       options : {
       },
       _create : function() {
            return this._super();
       },
       _renderMenu : function(ul, items) {

       },
       _renderItem : function(ul, item) {
           var li = $("<li>");
           this._setText(li, item.label);
           return li.appendTo(ul);
       }

   });
});