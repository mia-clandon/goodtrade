define(['jquery','jquery-ui', 'widgets/ui-sidebar'],function($) {
    return $.widget('ui.layout', {
        options : {
            navbar : null,
            sidebar : null,
            pageWrap : null,
            breadCrumbs : null,
            bottomControls : null,
        },
       _create: function() {
            this.options.breadCrumbs = $('#breadcrumbs');
            this.options.pageWrap = $('#page-wrap');
            this.options.sidebar = $('#sidebar');
            this.options.bottomControls = $('#bottom-controls');
       },
       _init : function() {
            var $sidebar = this.option('sidebar');
            $sidebar.sidebar();
            this._on($sidebar, {'sidebarshow' : this._handleSidebarShow});
            this._on($sidebar, {'sidebarhide' : this._handleSidebarHide});

           // скрол до регистрации.
           $("a[href='#footer-form']").click(function() {
               $('html, body').animate({
                   scrollTop: $("#register-form").offset().top
               }, 900);
           });
       },
       _destroy : function() {
            var $sidebar = this.option('sidebar');
           $sidebar.sidebar('destroy');
           this._off($sidebar, 'sidebarshow');
           this._off($sidebar, 'sidebarhide');
       },
        _handleSidebarShow : function() {
            var $body = $('body');
            this._addClass($body, 'is-shifted');
        },
        _handleSidebarHide : function () {
            var $body = $('body');
            this._removeClass($body, 'is-shifted');
        }
    });
});