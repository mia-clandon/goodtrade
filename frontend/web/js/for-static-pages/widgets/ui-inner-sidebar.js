define(['jquery', 'jquery-ui'], function($){
   return $.widget('ui.innerSidebar', {
       options : {
           menu : null
       },
        _create : function() {
            var $root = this.element;
            this.options.menu = $root.find('.inner-sidebar-menu');
            this._on(window, {'scroll' : this._handleScroll});
        },
        _destroy : function() {

        },
        _handleScroll : function(e) {

        }
   });
});