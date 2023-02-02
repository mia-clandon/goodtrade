/**
 * Плавающие вкладки.
 * @author Kenzhegulov Madiyar, Селюцкий Викентий razrabotchik.www@gmail.com
 * $(selector).affix();
 */
export default $.widget('ui.affix', {
    options : {
        rootOffset : 0,
        rootHeight : 0,
        headerHeight : 0,
        spysOffsets : [],
        selectors : {
            header : 'header',
            panel : '[data-type="affix-panel"]',
            buttons : '[data-type="affix-button"]',
            spys : '[data-type="affix-spy"]'
        }
    },
    _create : function() {},
    _init : function() {
        let $root = this.element,
            $panel = $root.find(this.options.selectors.panel),
            panelHeight = $panel.outerHeight(),
            $float = $panel
                .clone()
                .prependTo($root)
                .width($panel.width())
                .addClass("is-absolute"),
            $buttons = $root.find(this.options.selectors.buttons);

        $buttons.eq(0).addClass('is-active');

        this._on(window, {'scroll' : this._handleScroll});
        this._on(window, {'resize' : this.refresh});
        this._on($buttons, {'click' : this._handleClick});
        this.option({
            panel : $panel,
            panelHeight : panelHeight,
            float : $float,
            buttons : $buttons
        });
    },
    _destroy : function() {
        this._off(window, 'scroll');
    },
    refresh : function() {
        let $root = this.element,
            $spys = $root.find(this.options.selectors.spys),
            spysOffsets = [],
            rootOffset = $root.offset().top,
            rootHeight = $root.outerHeight(),
            headerHeight = $(this.options.selectors.header).outerHeight(),
            $panel = this.option('panel'),
            $float = this.option('float');

        $.each($spys, function (index, element) {
            spysOffsets.push(Math.floor($(element).offset().top));
        });

        this.option({
            rootOffset : rootOffset,
            rootHeight : rootHeight,
            headerHeight : headerHeight,
            spysOffsets : spysOffsets,
        });

        $float.width($panel.width());
    },
    _handleScroll : function() {
        this.refresh();

        let $root = this.element,
            scrollTop = $(window).scrollTop(),
            spysOffsets = this.option('spysOffsets'),
            rootOffset = this.option('rootOffset'),
            rootHeight = this.option('rootHeight'),
            headerHeight = this.option('headerHeight'),
            panelHeight = this.option('panelHeight'),
            $buttons = this.option('buttons'),
            $float = this.option('float');

        // Проверяем, чтобы позиция прокрутки была в пределах высоты главного контейнера
        if ((scrollTop > (rootOffset - headerHeight)) && (scrollTop < (rootOffset + rootHeight - headerHeight - panelHeight))) {
            for (let i = 0; i  < spysOffsets.length; i++) {
                if (scrollTop >= spysOffsets[i] - headerHeight - panelHeight) {
                    this._removeClass($buttons, 'is-active');
                    this._addClass($buttons.eq(i), 'is-active');
                }
            }

            if (scrollTop < spysOffsets[0] - panelHeight * 2) {
                this._removeClass($buttons, 'is-active');
                this._addClass($buttons.eq(0), 'is-active');
            }

            this._addClass($float, 'is-fixed');
            this._removeClass($float, 'is-absolute');
            $float.css({top : headerHeight});
        } else {
            this._addClass($root, 'is-relative');
            this._removeClass($float, 'is-fixed');
            this._addClass($float, 'is-absolute');
            $buttons.removeClass('is-active');
            $buttons.eq(0).addClass('is-active');
            $float.css({top: 0});
        }
    },
    // В frontend/app/pages/b2b/common.js есть обработчик нажатия на все элементы.
    // Необходимо сверять селектор кнопки там и в этом виджете.
    _handleClick : function (e) {
        let $target = $(e.target.hash),
            headerHeight = this.option('headerHeight'),
            panelHeight = this.option('panelHeight');

        $('html, body').animate({
            // Координата заголовка минус шапка и панель
            scrollTop: $target.offset().top - headerHeight - panelHeight
        }, 1000);

        e.preventDefault();
    }
});