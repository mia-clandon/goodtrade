/**
 * Плавающий элемент.
 * @author Селюцкий Викентий razrabotchik.www@gmail.com
 * $(selector).floatElement({floatToElement: $(selector)});
 */
export default $.widget('ui.floatElement', {
    options : {
        floatElement: null,
        floatToElement: document,
        header: null
    },
    _create : function() {},
    _init : function() {
        let $root = this.element,
            $float = $root.clone()
                .hide()
                .prependTo($root.parent())
                .width($root.width())
                .addClass("is-absolute")
                .css("top", "0");

        $root.parent().addClass("is-relative");
        this.options.floatElement = $float;
        this.options.header = $('header');
        this._on(window, {'scroll' : this._handleScroll});
    },
    _destroy : function() {
        this._off(window, 'scroll');
    },
    _handleScroll : function() {
        let $root = this.element,
            $float = this.options.floatElement,
            $floatTo = this.options.floatToElement,
            parentOffsetTop = $root.parent().offset().top,
            floatToOffsetTop = $floatTo.offset().top,
            scrollTop = $(window).scrollTop(),
            headerHeight = 0,
            elementHeight = Math.ceil($root.outerHeight());

        if (this.options.header.length) {
            headerHeight = this.options.header.outerHeight();
        }

        if ($root.outerHeight(true) >= $root.parent().outerHeight(true) - 10 ||
            $root.outerHeight(true) >= $(window).height() - 10) {
            return false;
        }

        if ((scrollTop + headerHeight) >= parentOffsetTop) {
            $root.hide();
            $float.show();

            if ((scrollTop + headerHeight) >= (floatToOffsetTop - elementHeight)) {
                $float.removeClass('is-fixed')
                    .addClass('is-absolute')
                    .css({
                        'top' : '',
                        'bottom' : 0
                    });
            } else {
                $float.removeClass('is-absolute')
                    .addClass('is-fixed')
                    .css({
                        'top' : headerHeight,
                        'bottom' : ''
                    });
            }
        } else {
            $root.show();
            $float.removeClass('is-fixed')
                .addClass('is-absolute')
                .css('top', 0)
                .hide();
        }
    }
});