/**
 * Виджет сворачивания/разворачивания элемента по высоте.
 * @example Пример использования виджета
 * // $('.js-collapse').collapse();
 * @author Kenzhegulov Madiyar, Селюцкий Викентий razrabotchik.www@gmail.com
 */

export default $.widget('ui.collapse', {
    options: {
        open: false,
        content: null,
        toggle: null,
        start: 0,
        end: 0,
        duration: 200,
        startContentHeight : 0,
        disposable: false,

        selectors : {
            toggle : '.js-collapse-toggle',
            content : '.js-collapse-content',
            openClass : 'is-open'
        }
    },
    _create: function () {
        let $root = this.element,
            $toggle = this.options.toggle = $root.find(this.options.selectors.toggle);
        this.options.content = $root.find(this.options.selectors.content);
        this._on($toggle, {'click': this._handleClick});
    },
    _init: function () {
        let $root = this.element,
            $content = this.option('content');
        if ($root.hasClass(this.options.selectors.openClass) || $root.data('open')) {
            this.option('open', true);
        }
        $content.height(this.options.startContentHeight);
        this.options.start = $content.height();
        this.options.end = $content.prop('scrollHeight');
    },
    _destroy: function () {
        let $toggle = this.option('toggle');
        this._off($toggle, 'click');
    },
    _handleClick: function (e) {
        let currentState = this.option('open');
        this.option('open', !currentState);
        e.preventDefault();
    },
    _setOption: function (key, val) {
        let $root = this.element,
            $content = this.option('content'),
            start = this.option('start'),
            end = this.option('end') || $content.prop('scrollHeight'),
            duration = this.option('duration'),
            self = this;
        if (key == 'open') {
            if (val) {
                $content.animate({height: end}, {
                    duration: duration,
                    easing: 'linear',
                    complete: function () {
                        self._trigger('showend');
                        $(this).attr("style", "");

                        if (self.options.disposable) {
                            let $toggle = self.option('toggle');
                            $toggle.hide();
                        }
                    }
                });
                this._addClass($root, 'is-open');
                this._trigger('show');
            }
            else {
                $content.animate({height: start}, {
                    duration: duration,
                    easing: 'linear',
                    complete: function () {
                        self._trigger('hideend');

                        if (self.options.disposable) {
                            let $toggle = self.option('toggle');
                            $toggle.hide();
                        }
                    }
                });
                this._removeClass($root, 'is-open');
                this._trigger('hide');
            }
        }
        return this._super(key, val);
    }
});