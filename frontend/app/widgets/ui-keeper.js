/**
 * Виджет добавляет в cookie значение из data-id в cookie по ключу key.
 * @author Артём Широких kowapssupport@gmail.com
 */
export default $.widget('ui.keeper', {
    options: {
        active: false,
        need_show_count: true,
        options: {
            expires: 7,
            path: '/',
        }
    },
    _create: function () {
        let $root = this.element;
        this.options.need_show_count = $root.attr('data-need-show-count') == undefined;
        this._on($root, {'click': this._handleClick});
        $.cookie.json = true;
    },
    _init: function () {
        let $root = this.element,
            key = $root.attr('data-key'),
            id = parseInt($root.attr('data-id')),
            cookie = $.cookie(key) || [],
            $counter = $('#' + key);
        if (this.options.need_show_count && key != undefined) {
            if ($.inArray(id, cookie) > -1) {
                this.option('active', true);
            }
            if (cookie.length) {
                $counter.show();
            }
            else {
                $counter.hide();
            }
        }
    },
    _destroy: function () {
        let $root = this.element;
        $root._off('click');
    },
    _handleClick: function (e) {
        let $root = this.element,
            id = $root.attr('data-id'),
            key = $root.attr('data-key');

        let ids = id.split(',');
        for (let i = 0; i < ids.length; i++) {
            this.set(key, parseInt(ids[i]));
        }
        this.element.trigger('updateCompare', {id: id, key: key});
        e.preventDefault();
    },
    set: function (key, id) {
        let $counter = $('#' + key),
            cookie = $.cookie(key) || [];
        if ($.inArray(id, cookie) > -1) {
            cookie.splice(cookie.indexOf(id), 1);
            this.option('active', false);
        }
        else {
            cookie.push(id);
            this.option('active', true);
        }
        if (cookie.length) {
            $counter.show();
        }
        else {
            $counter.hide();
        }
        $.cookie(key, cookie, this.options.options);
    },
    _setOption: function (key, val) {
        let $root = this.element,
            cookie_key = $root.attr('data-key'),
            id = $root.attr('data-id'),
            ids = id.split(','),
            $instances = $(':data("ui-keeper")').filter('[data-key="' + cookie_key + '"]'),
            $keepers = $("");

        for (let i = 0; i < ids.length; i++ ) {
            $keepers = $keepers.add($instances.filter('[data-id="' + ids[i] + '"]'));
        }

        if (key == 'active') {
            if (val) {
                this._addClass($keepers, 'is-active');
            }
            else {
                this._removeClass($keepers, 'is-active');
            }
        }
        this._super(key, val);
    }
});