import './ui-tags-product';
import './ui-animation-mixin';

export default $.widget('ui.product', {
    options: {
        activeMenuItem: null,
        activeSubMenuItem: null
    },
    _create: function () {
        this.wrap = this.element.children('.ui-product-menu-wrap');
        this.menu = this.wrap.children('.ui-product-menu');

        this._on(this.wrap, {
            'click .ui-product-menu-back': '_handleMenuBackClick'
        });
        this._on(this.menu, {
            'click .ui-product-menu-item': '_handleMenuItemClick',
            'click .ui-product-submenu-item': '_handleSubMenuItemClick',
            'click .ui-product-submenu-submenu-item': '_handleSubSubMenuItemClick'
        })
    },

    _initTags: function () {

        var $tags = this.element.children('.ui-tags');

        if ($tags.length === 0) {
            this.tags = this._renderTags().prependTo(this.element);
        } else {
            this.tags = $tags.tagsProduct();
        }
        this._on(this.tags, {
            'tagsproductaddtag': '_handleAddTag',
            'tagsproductremovetag': '_handleRemoveTag'
        });
    },
    _handleAddTag: function (event, ui) {
        this._trigger('addProduct', ui);
    },
    _handleRemoveTag: function (event, ui) {
        this._trigger('removeProduct', ui);
    },
    addItem: function (item) {
        this.tagsProduct('addTag', item);
    },
    selectItem: function () {},
    collapse: function (item) {},
    addCategory: function (item) {},
    selectMenuItem: function (item) {
        this.option('activeMenuItem', item);
    },
    _getMenuItems: function (id) {
        $.getJSON({url: '../public/api/category'})
            .done(function (data) {
                console.log(data);
            });
    },
    _handleMenuItemClick: function (event) {
        var $el = $(event.currentTarget),
            $selected = this.menu.find('ui-product-menu-item-selected');
        // Если это тот же элемент
        if ($selected.is($el)) {
            return false;
        }
    },
    _handleSubMenuItemClick: function (event) {
        var $el = $(event.currentTarget);
        if ($el.has('ul').length !== 0) {
            this._addClass($el, 'ui-product-submenu-item-selected');
            return;
        }
        selectItem($el.data());
    },
    _handleSubSubMenuItemClick: function (event) {
        var $el = $(event.currentTarget);
        this.item[2] = $el.data();
    },
    _handleMenuBackClick: function (event) {
        this.menu.children('li').children('ul').children('li').fadeIn().removeClass('ui-product-menu-submenu-item-active');
        event.preventDefault();
    },
    _renderTags: function () {
        return $.ui.tagsProduct();
    },
    _renderMenu: function (items) {
        var $ul = $('<ul/>');
        this._addClass($ul, 'ui-product-menu');
        $.each(items, function (i, item) {
            this._renderMenuItem(item).appendTo($ul);
        });
        return $ul;
    },
    _renderMenuItem: function (item) {
        var $li = $('<li/>');
        $li.attr('value', item.value)
            .text(item.label);
        this._addClass($li, 'ui-product-menu-item');
        return $li;
    },
    _renderSubMenu: function () {
        var $ul = $('<ul/>');
        this._addClass($ul, 'ui-product-submenu');
        $.each(items, function (i, item) {
            this._renderMenuItem(item).appendTo($ul);
        });
        return $ul;
    },
    _renderSubMenuItem: function () {
        var $li = $('<li/>');
        $li.attr('value', item.value)
            .text(item.label);
        this._addClass($li, 'ui-product-submenu-item');
        return $li;
    },
    _setOption: function (key, val) {
        if (key === 'activeMenuItem') {
            this._removeClass(val.siblings(), 'ui-product-menu-item-active');
            this._addClass(val, 'ui-product-menu-item-active');
        }
        if (key === 'activeSubMenuItem') {
            this._removeClass(val.siblings().hide(), 'ui-product-submenu-item-active');
            this._addClass(val, 'ui-product-submenu-item-active');
        }
        return this._super(key, val);
    }
});