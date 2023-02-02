/**
 * Плавающий элемент.
 * @author Селюцкий Викентий razrabotchik.www@gmail.com
 * $(selector).floatElement({floatToElement: $(selector)});
 */
export default $.widget('ui.floatElement', {
    options : {
        elementOffset: null,
        floatElement: null,
        floatToElement: document
    },
    _create : function() {
    },
    _init : function() {
        let $root = this.element,
            $float = $root.clone()
                .hide()
                .prependTo($root.parent())
                .width($root.width())
                .addClass("is-absolute")
                .css("top", "0");

        $root.parent().css("position", "relative");
        this.options.elementOffset = $root.offset().top;
        this.options.floatElement = $float;
        this._on(window, {'scroll' : this._handleScroll});
    },
    _destroy : function() {
        this._off(window, 'scroll');
    },
    _handleScroll : function() {
        let $root = this.element,
            $float = this.options.floatElement,
            elementOffset = this.options.elementOffset,
            floatToOffset = this.options.floatToElement.offset().top,
            scrollTop = $(window).scrollTop(),
            navbarHeight = $('nav').outerHeight(),
            elementHeight = $root.outerHeight();

        if ((scrollTop + navbarHeight) >= elementOffset) {
            $root.hide();
            $float.show();

            if ((scrollTop + navbarHeight) >= (floatToOffset - elementHeight)) {
                this._removeClass($float, 'is-fixed');
                this._addClass($float, 'is-absolute');
                $float.css("top", floatToOffset - elementOffset - elementHeight);
            } else {
                this._removeClass($float, 'is-absolute');
                this._addClass($float, 'is-fixed');
                $float.css("top", navbarHeight);
            }
        } else {
            $root.show();
            this._removeClass($float, 'is-fixed');
            this._addClass($float, 'is-absolute');
            $float.css("top", 0);
            $float.hide();
        }
    }
});