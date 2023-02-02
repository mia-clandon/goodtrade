export default $.widget('ui.dropdownMenu', {
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
        toogleClass: "dropdown-toggle",
        $navbar: $('#navbar'),
        menuClass: "dropdown-menu",
        menuBodyClass: "dropdown-menu-body",
        menuBodyFadedClass: "menu-body-faded",
        menuContentClass: "dropdown-menu-content",
        hasScrollBarClass: "has-scrollbar"
    },
    _create: function () {
        let $root = this.element,
            $toggle = $root.find("." + this.options.toogleClass),
            $navbar = this.options.$navbar,
            $self = this;

        this._on($toggle, {'click': this._handleClick});
        this._on($navbar, {'navbarhide': this.hide});
    },
    _init: function () {
        let $root = this.element,
            $body = $root.find("." + this.options.menuBodyClass),
            $content = $root.find("." + this.options.menuContentClass);

        // Подключение красивой прокрутки, если список большой
        if ($body.hasClass(this.options.hasScrollBarClass)) {
            $content.perfectScrollbar();

            // Если список не вмещается, то внизу добавлять градиент
            $content.on("scroll", function (e) {
                if ( e.target.scrollTop === (e.target.scrollHeight - e.target.offsetHeight) ) {
                    $body.removeClass(this.options.menuBodyFadedClass);
                } else {
                    $body.addClass(this.options.menuBodyFadedClass);
                }
            });
        }
    },
    _destroy: function () {
        let $root = this.element,
            $toggle = $root.find("." + this.options.toogleClass),
            $body = $root.find("." + this.options.menuBodyClass),
            $content = $root.find("." + this.options.menuContentClass),
            $menu = $root.find("." + this.options.menuClass);

        this._hide($menu, false);

        if ($body.hasClass(this.options.hasScrollBarClass)) {
            $content.perfectScrollbar('destroy');
        }

        this._off($toggle, 'click');
    },
    _handleClick: function (e) {
        let open = this.option('open');

        this.option('open', !open);

        return false;
    },
    hide: function () {
        this.option('open', false);
    },
    _setOption: function (key, val) {
        let $root = this.element,
            $menu = $root.find("." + this.options.menuClass),
            $body = $root.find("." + this.options.menuBodyClass),
            hide = this.option('hide'),
            show = this.option('show'),
            $instances = $(':data("ui-dropdownMenu")').not($root),
            $self = this;

        if (key === 'open') {
            if (val) {
                if ($body.hasClass(this.options.hasScrollBarClass)) {
                    this._show($menu, show, function () {
                        $body.perfectScrollbar();
                    });
                } else {
                    this._show($menu, show);
                }

                // Скрытие прочих выпадающих меню
                $instances.dropdownMenu('hide');

                $('body').on('click', function (e) {
                    let $el = $(e.target);
                    if ($menu.has($el).length || $el.is($menu)) {
                        e.stopPropagation();
                    }
                    else {
                        $self.hide();
                        $('body').off(e);
                    }
                });
            }
            else {
                this._hide($menu, hide);
            }
        }
        return this._super(key, val);
    }
});