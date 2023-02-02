/**
 * Маленькие модальные окна (например для коммерческого запроса и обратного звонка).
 * @author Артём Широких kowapssupport@gmail.com
 */
export default $.widget('ui.popup', {
    options: {
        visible: false,
        verticalAlign: 'top',
        horizontalAlign: 'left',
        // элемент, при нажатии на который будет открываться модальное окно
        toggleSelector: '.popup-toggle',
        // элемент, внутри которого находится переключатель и будет появляться модальное окно
        wrapperSelector: '.popup-dropdown',
        // элемент, при нажатии на который происходит событие отмены.
        cancelSelector: '#popup-cancel',
        // элемент, при нажатии на который происходит событие отправки.
        sendSelector: '#popup-send',
        data: {}
    },
    _create: function () {
        let th = this,
            $root = this.element,

            $cancel = $root.find(this.options.cancelSelector),
            $send = $root.find(this.options.sendSelector)
            ;
        // события окна.
        this._on($send, {'click': this.send});
        this._on($cancel, {'click': this.cancel});
        // открытие окна.
        $('body').on('click', this.options.toggleSelector, function (e) {
            th.handleClick(e);
        });
    },
    _init: function () {},
    _destroy: function () {},
    refresh: function () {},
    send: function () {},
    // события окна.
    show: function () {},
    close: function () {},
    /**
     * Закрытие модального окна.
     */
    cancel: function () {
        this.refresh();
        this.option({
            visible: false
        });

        this.element.css({
            "position" : "",
            "top" : "",
            "right" : "",
            "bottom" : "",
            "left" : "",
            "box-shadow" : "",
            "width" : "",
            "border-radius" : ""
        }).perfectScrollbar('destroy');

        $("body").css({
            "overflow" : ""
        });
    },
    /**
     * Открытие модального окна.
     * @param e
     */
    handleClick: function (e) {
        let $root = this.element,
            $el = $(e.currentTarget),
            $wrap = $el.closest(this.options.wrapperSelector),
            // Значения взяты из frontend/web/scss/b2b/bootstrap/_variables.scss. Проверяйте на актуальность.
            breakpoints = {
                xs: 0,
                sm: 360,
                md: 768,
                lg: 1360,
                xl: 1920
            };

        $wrap.append($root);
        // data атрибуты с кнопки вызвавшей модальное окно.
        this.options.data = $el.data();
        this.option({
            verticalAlign: $el.data('vertical-align') || this.options.verticalAlign,
            horizontalAlign: $el.data('horizontal-align') || this.options.horizontalAlign,
            visible: true
        });

        // Изменяем внешний вид модального окна для смартфонов
        if ($(window).outerWidth() < breakpoints.lg) {
            $root.css({
                "position" : "fixed",
                "top" : 0 || $(".bar-top").outerHeight(),
                "right" : "0",
                "bottom" : 0 || $(".bar-bottom").outerHeight(),
                "left" : "0",
                "box-shadow" : "none",
                "width" : "auto",
                "border-radius" : "0"
            }).perfectScrollbar();

            $("body").css({
                "overflow" : "hidden"
            });

        }

        e.stopPropagation();

        // Отмена прокрутки вверх при клике по ссылке с href="#"
        if ($el.is('a') && $el.attr('href') === '#') {
            e.preventDefault();
        }
    },
    /**
     * Установка опций.
     * @param key
     * @param val
     * @returns {*}
     * @private
     */
    _setOption: function (key, val) {
        let $root = this.element,
            $self = this;
        if (key === 'verticalAlign') {
            switch (val) {
                case 'top' :
                    $root.css({top: '0px', bottom: 'auto'});
                    break;
                case 'bottom' :
                    $root.css({bottom: '0px', top: 'auto'});
                    break;
                default :
                    $root.css({top: '0px', bottom: 'auto'});
                    break;
            }
        }
        else if (key === 'horizontalAlign') {
            switch (val) {
                case 'left' :
                    $root.css({left: '0px', right: 'auto'});
                    break;
                case 'right' :
                    $root.css({right: '0px', left: 'auto'});
                    break;
                default :
                    $root.css({left: '0px', right: 'auto'});
                    break;
            }
        }
        else if (key === 'visible') {
            if (val) {

                this._removeClass('is-hidden');
                this.show();

                $('body').on('click', function (e) {
                    let $el = $(e.target);
                    if ($root.is($el) || $root.has($el).length) {
                        return false;
                    }
                    else {
                        if (!$self.option('disabled')) {
                            $self.option('visible', false);
                        }
                        $('body').off(e);
                    }
                });
            }
            else {
                this.close();
                this._addClass('is-hidden');
            }
        }
        return this._super(key, val);
    }
});