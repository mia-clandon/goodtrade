/**
 * Маленькие модальные окна (например для коммерческого запроса и обратного звонка).
 * @author Артём Широких kowapssupport@gmail.com
 */
define(['jquery', 'jquery-ui'], function ($) {
    return $.widget('ui.popup', {
        options: {
            visible: false,
            verticalAlign: 'top',
            horizontalAlign: 'left',
            // элемент, при нажатии на который будет открываться модальное окно
            toggleSelector: '.popup-toggle',
            // элемент, внутри которого находится переключатель и будет появляеться модальное окно
            wrapperSelector: '.popup-dropdown',
            // элемент, при нажатии на который происходит событие отмены.
            cancelSelector: '#popup-cancel',
            // элемент, при нажатии на который происходит событие отправки.
            sendSelector: '#popup-send',
            data: {}
        },
        _create: function () {
            var th = this,
                $root = this.element,
                $toggle = $(this.options.toggleSelector),

                $cancel = $root.find(this.options.cancelSelector),
                $send = $root.find(this.options.sendSelector)
            ;
            // события окна.
            this._on($send, {'click': this.send});
            this._on($cancel, {'click': this.cancel});
            //this._on($toggle, {'click': this._handleClick});
            // открытие окна.
            $('body').on('click', this.options.toggleSelector, function (e) {
                th.handleClick(e);
            });
        },
        _init: function () {
        },
        _destroy: function () {
        },
        refresh: function () {
        },
        send: function () {
        },
        // события окна.
        show: function () {
        },
        close: function () {
        },
        /**
         * Закрытие модального окна.
         */
        cancel: function () {
            this.refresh();
            this.option({
                visible: false
            });
        },
        /**
         * Открытие модального окна.
         * @param e
         */
        handleClick: function (e) {
            var $root = this.element,
                $el = $(e.currentTarget),
                $wrap = $el.closest(this.options.wrapperSelector);

            $wrap.append($root);
            // data атрибуты с кнопки вызвавшей модальное окно.
            this.options.data = $el.data();
            this.option({
                verticalAlign: $el.data('vertical-align') || this.options.verticalAlign,
                horizontalAlign: $el.data('horizontal-align') || this.options.horizontalAlign,
                visible: true
            });
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
            var $root = this.element,
                $self = this;
            if (key == 'verticalAlign') {
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
            else if (key == 'horizontalAlign') {
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
            else if (key == 'visible') {
                if (val) {

                    this._removeClass('is-hidden');
                    this.show();

                    $('body').on('click', function (e) {
                        var $el = $(e.target);
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
});