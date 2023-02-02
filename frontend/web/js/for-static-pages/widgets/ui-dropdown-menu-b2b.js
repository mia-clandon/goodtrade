define(['jquery', 'jquery-ui', 'jquery.perfectScrollbar'], function($) {
    return $.widget('ui.dropdownMenu', {
        options: {
            hide: {
                effect: 'slideUp',
                duration: 200,
                easing: 'easeInQuad',
                queue: 'dropdown'
            },
            show: {
                effect: 'slideDown',
                duration: 200,
                easing: 'easeInQuad',
                queue: 'dropdown'
            },
            open: false,
            $navbar: $('#navbar'),
            indicatorPosition : "auto", // auto - скрипт позицию задаёт, manual - позиция средствами CSS

            classes: {
                toggleClass: "dropdown__toggle",
                itemClass: "dropdown__item",
                itemVisibleClass: "dropdown__item_visible",
                indicatorClass: "dropdown__item-indicator",
                itemBodyClass: "dropdown__item-body",
                itemBodyFadedClass: "dropdown__item-body_faded",
                itemContentClass: "dropdown__item-content",
            }
        },
        _create: function () {
            var $root = this.element,
                $navbar = this.options.$navbar,
                $toggle = $root.find("." + this.options.classes.toggleClass);

            this._on($toggle, {'click': this._handleClick});
            this._on($navbar, {'navbarhide': this.hide});
        },
        _init: function () {
            var self = this,
                $root = this.element,
                $item = $root.find("." + this.options.classes.itemClass);

            // Сжимаю выпадающие элементы на случай, если они выходят за границы окна, чтобы не было прокрутки
            $item.css({
                "margin" : 0,
                "padding" : 0,
                "width" : 0,
                "overflow" : "hidden"
            });
        },
        _destroy: function () {
            var $root = this.element,
                $toggle = $root.find("." + this.options.classes.toggleClass),
                $body = $root.find("." + this.options.classes.itemBodyClass),
                $content = $root.find("." + this.options.classes.itemContentClass),
                $item = $root.find("." + this.options.classes.itemClass);

            this._hide($item, false);

            $content.perfectScrollbar('destroy');

            this._off($toggle, 'click');
        },
        _handleClick: function () {
            var open = this.option('open');

            this.option('open', !open);

            return false;
        },
        hide: function () {
            this.option('open', false);
        },
        _setOption: function (key, val) {
            var self = this,
                $root = this.element,
                $toggle = $root.find("." + this.options.classes.toggleClass),
                $item = $root.find("." + this.options.classes.itemClass),
                $indicator = $root.find("." + this.options.classes.indicatorClass),
                $body = $root.find("." + this.options.classes.itemBodyClass),
                $content = $root.find("." + this.options.classes.itemContentClass),
                hide = this.option('hide'),
                show = this.option('show'),
                $instances = $(':data("ui-dropdownMenu")').not($root);

            if (key === 'open') {
                if (val) {
                    $item.css({
                        "margin" : "",
                        "padding" : "",
                        "width" : "",
                        "overflow" : ""
                    });

                    var menuPosR = $item.outerWidth() + $item.offset().left,
                        menuCssPosL = parseInt($item.css("left"));

                    if (isNaN(menuCssPosL)) {menuCssPosL = 0}

                    // Если всплывающий элемент выходит за границу окна, то его позицию смещать до отступа справа в 15
                    // пикселей
                    if (menuPosR > $(window).width()) {
                        $item.css({
                            "left" : menuCssPosL - (menuPosR - $(window).width()) - 15
                        });

                        // Если после смещения влево выпадающий элемент вышел за границу экрана, то это значит, что
                        // экран маленький и нужно сжать выпадающий элемент, разместив его с отступами по 15 пикселей
                        // слева и справа
                        if ($item.offset().left < 0) {
                            $item.css({
                                "max-width" : $(window).width() - 30,
                                "left" : 15
                            })
                        }
                    }

                    // Если позиция индикатора регулируется скриптом
                    if (this.options.indicatorPosition === "auto") {
                        var menuPosL = $item.offset().left,
                            togglePosL = $toggle.offset().left;

                        if (togglePosL >= menuPosL) {
                            var toggleWidth = $toggle.width(),
                                indicatorWidth = Math.round($($indicator)[0].getBoundingClientRect().width),
                                indicatorPosL = Math.round(togglePosL - menuPosL + ((toggleWidth - indicatorWidth) / 2) + 1);

                            $("." + this.options.classes.indicatorClass).css({
                                "left" : indicatorPosL
                            });
                        }
                    }

                    $item.hide(); // Задаём элементу display:none, чтобы корректно отработал slideDown ниже
                    $item.addClass(this.options.classes.itemVisibleClass);

                    this._show($item, show, function () {
                        // Подключение красивой прокрутки
                        $content.perfectScrollbar();

                        // Если список не вмещается, то внизу добавлять градиент
                        if ( ($content.length > 0) && ($content[0].scrollHeight > $content[0].clientHeight) ) {
                            $body.addClass(self.options.classes.itemBodyFadedClass);
                        }

                        // Убирать градиент, если достигнут конец прокрутки
                        $content.on("scroll", function (e) {
                            if ( e.target.scrollTop === (e.target.scrollHeight - e.target.offsetHeight) ) {
                                $body.removeClass(self.options.classes.itemBodyFadedClass);
                            } else {
                                $body.addClass(self.options.classes.itemBodyFadedClass);
                            }
                        });
                    });

                    // Скрытие прочих выпадающих меню
                    $instances.dropdownMenu('hide');

                    $('body').on('click', function (e) {
                        var $el = $(e.target);
                        if ($item.has($el).length || $el.is($item)) {
                            e.stopPropagation();
                        }
                        else {
                            self.hide();
                            $('body').off(e);
                        }
                    });
                }
                else {
                    this._hide($item, hide, function () {
                        $item.removeClass(self.options.classes.itemVisibleClass);

                        // Сжимаю выпадающие элементы на случай, если они выходят за границы окна и возвращаю исходные
                        // значения
                        $item.css({
                            "margin" : 0,
                            "padding" : 0,
                            "width" : 0,
                            "overflow" : "hidden",

                            "max-width" : "",
                            "left" : ""
                        });
                        $item.show();

                        // Отключение прокрутки
                        $content.perfectScrollbar('destroy');
                        $content.off("scroll");
                    });
                }
            }
            return this._super(key, val);
        }
    });
});