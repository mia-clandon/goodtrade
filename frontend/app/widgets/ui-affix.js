/**
 * Плавающие вкладки.
 * @author Kenzhegulov Madiyar, Селюцкий Викентий razrabotchik.www@gmail.com
 * $(selector).affix();
 */
export default $.widget('ui.affix', {
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
        /*position : 0,
        classes : {
            header : 'js-affix-header',
            content : 'js-affix-content',
            buttons : 'js-affix-button'
        }*/
    },
    _create : function() {
        /*let $root = this.element,
            headerClass = this.options.classes.header,
            contentClass = this.options.classes.content,
            buttonClass = this.options.classes.button,
            $header = this.options.header = $root.find(headerClass);
            this.options.content = $root.find(contentClass);
            this.options.buttons = $header.find('a');*/
    },
    _init : function() {
        let $root = this.element,
            $header = $root.find('.js-affix-float'),
            $float = $header
                .clone()
                .prependTo($root)
                .width($header.width())
                .addClass("is-absolute"),
            $tabs = $root.find('.js-affix-tab');
        this.refresh();
        // this._on($root, {'click' : this._handleClick});
        this._on(window, {'scroll' : this._handleScroll});
        this.option({
            float : $float,
            tabs : $tabs
        });
    },
    _destroy : function() {
        this._off(window, 'scroll');
    },
    refresh : function() {
        let $root = this.element,
            $spys = $root.find('.js-affix-spy'),
            offsets = [],
            containerOffset = $root.offset().top,
            containerHeight = $root.outerHeight(),
            navbarHeight = $('nav').outerHeight(),
            headerHeight = $root.find('.js-affix-float').outerHeight();

        $.each($spys, function (index, element) {
            offsets.push($(element).offset().top);
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
        this.refresh();

        let offsets = this.option('offsets'),
            containerOffset = this.option('containerOffset'),
            containerHeight = this.option('containerHeight'),
            navbarHeight = this.option('navbarHeight'),
            headerHeight = this.option('headerHeight'),
            scrollTop = $(window).scrollTop(),
            $root = this.element,
            $tabs = $root.find('.js-affix-tab'),
            //position = 0,
            $float = this.option('float');

        if ((scrollTop > (containerOffset - navbarHeight)) && (scrollTop < (containerOffset + containerHeight - navbarHeight - headerHeight))) {
            for (let i = 0; i  < offsets.length; i++) {
                if (offsets[i] - headerHeight * 2 < scrollTop) {
                    this._removeClass($tabs, 'is-active');
                    this._addClass($tabs.eq(i), 'is-active');
                }
            }
            this._addClass($float, 'is-fixed');
            this._removeClass($float, 'is-absolute');
            $float.css({top : 60});
        } else {
            this._addClass($root, 'is-relative');
            this._removeClass($float, 'is-fixed');
            this._addClass($float, 'is-absolute');
            $tabs.removeClass('is-active');
            $tabs.eq(0).addClass('is-active');
            $float.css({top: 0});
        }
    },
    _setOption : function(key, val){
        let $tabs = this.option('tabs');

        if(key === 'currentPosition') {
            this._removeClass($tabs, 'is-active');
            this._addClass($tabs.eq(val), 'is-active');
        }

        return this._super(key, val);
    }
});