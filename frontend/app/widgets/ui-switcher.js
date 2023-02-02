/**
 * Переключатель для окон,
 * работает по принципу отображения блока по нажатию на ссылку с href="#block"
 * отобразит блок с data-id="block".
 * @author Артём Широких kowapssupport@gmail.com
 */
export default $.widget('ui.switcher', {
    options: {
        selector: '*'
    },
    _create: function () {
        var self_object = this;
        this.items = this.element.children(this.options.selector);
        this._on({'click .ui-switcher-toggle': '_handleClick'});
    },
    widget: function () {
        return this.element;
    },
    refresh: function () {
        this.items
            .hide()
            .eq(0)
            .show();
        this._resetForms();
    },
    _destroy: function () {
        this._off(this.element, 'click');
    },
    _resetForms: function () {
        $.each(this.items, function (i, form) {
            form.reset();
        });
    },
    _handleClick: function (event) {
        // Обрабатываем только ссылки.
        if (event.target.nodeType !== 1 && event.target.nodeName !== 'A') {
            return;
        }
        var hash = event.target.hash,
            $target = $('[data-id=' + hash.replace('#', '') + ']'),
            $active = this.items.filter(':visible');
        // Если это ссылается на ту же форму.
        if ($target.is($active)) {
            return;
        }
        else {
            this._resetForms();
            $active.hide();
            $target.show();
        }
        event.preventDefault();
    }
});