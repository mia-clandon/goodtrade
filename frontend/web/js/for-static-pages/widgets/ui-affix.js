define(['jquery','jquery-ui'],function($){
   return $.widget('ui.affix',{
     options : {
       header : null,
       content : null,
       buttons : null,
       offsetTop : 0,
       width : 0,
       height : 0,
       delimeter : 0,
       margin : 0,
       offsets : [],
       containerOffset : 0,
       containerHeight : 0,
       navbarHeight : 0,
       position : 0,
       classes : {
          header : 'js-affix-header',
          content : 'js-affix-content',
          buttons : 'js-affix-button'
       }
     },
     _create : function() {
         var $root = this.element,
             headerClass = this.options.classes.header,
             contentClass = this.options.classes.content,
             buttonClass = this.options.classes.button,
             $header = this.options.header = $root.find(headerClass);
             this.options.content = $root.find(contentClass);
             this.options.buttons = $header.find('a');
     },
     _init : function() {
         var $root = this.element,
             $header = $root.find('.js-affix-float'),
             $float = $header
                 .clone()
                 .prependTo($root)
                 .width($header.width()),
             $tabs = $root.find('.js-affix-tab');
         this.refresh();
         this._on(window, {'scroll' : this._handleScroll});
         this.option({float : $float, tabs : $tabs});
     },
     _destroy : function() {
         this._off(window, 'scroll');
     },
     refresh : function() {
          var $root = this.element,
              $spys = $root.find('.js-affix-spy'),
              offsets = [],
              containerOffset = $root.offset().top,
              containerHeight = $root.height(),
              navbarHeight = $('nav').height(),
              headerHeight = $root.find('.js-affix-float').height();
        $.each($spys, function(i, obj) {
            offsets.push($(obj).offset().top);
         });
        this.option({
            offsets : offsets,
            containerOffset : containerOffset,
            containerHeight : containerHeight,
            navbarHeight : navbarHeight,
            headerHeight : headerHeight
        });
     },
     _handleScroll : function() {
        var offsets = this.option('offsets'),
            containerOffset = this.option('containerOffset'),
            containerHeight = this.option('containerHeight'),
            navbarHeight = this.option('navbarHeight'),
            headerHeight = this.option('headerHeight'),
            scrollTop = $(window).scrollTop(),
            $root = this.element,
            $tabs = $root.find('.js-affix-tab'),
            position = 0,
            $float = this.option('float');
            if(scrollTop > containerOffset - navbarHeight && scrollTop < containerOffset + containerHeight - navbarHeight - headerHeight) {
                for(var i = 0; i  < offsets.length; i++) {
                    if(offsets[i] < scrollTop) {
                        this._removeClass($tabs, 'is-active');
                        this._addClass($tabs.eq(i), 'is-active');
                    } else {
                       continue;
                    }
                }
                this._addClass($float, 'is-fixed');
                this._removeClass($float, 'is-absolute');
                $float.css({top : 60});
            } else {
                this._addClass($root, 'is-relative');
                this._removeClass($float, 'is-fixed');
                this._addClass($float, 'is-absolute');
                $tabs.eq(0).addClass('is-active');
                $float.css({top: 0});
            }
     },
     _setOption : function(key, val){
        var $tabs = this.option('tabs');
        if(key == 'currentPosition') {
            this._removeClass($tabs, 'is-active');
            this._addClass($tabs.eq(val), 'is-active');
        }
         return this._super(key, val);
     }
   });
});