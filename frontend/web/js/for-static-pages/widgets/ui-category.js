define('widgets/ui-category', ['jquery', 'jquery-ui', 'widgets/ui-tags-category', 'widgets/ui-animation-mixin'], function ($) {
    return $.widget('ui.category-old', [$.ui.formResetMixin, $.ui.animationMixin, {
        defaultElement: '<div>',
        version: '1.0.0',
        options: {
            itemCount: 0
        },
        _create: function () {
            this._addClass('ui-front', 'ui-widget');
        },
        _init: function () {

            this._initButton();
            this._initTags();
            this._initMenu();

            // добавляю категории из input hidden (уже существующие).
            var $hidden = this.element.find('input:hidden'),
                activities = [],
                th = this;
            try {
                activities = JSON.parse($hidden.val());
            }
            catch (e) {
                activities = [];
            }
            activities.forEach(function(activity_id) {
                var label = th.element.find('.ui-category-menu-list li[data-value="'+activity_id+'"]').text();
                th.tags.tagsCategory('addTag', {label: label, value: activity_id.toString()});
            });

            $('<div/>', {
                'class': 'ui-category-overflow'
            }).appendTo(this.element)
              .append(this.tags, this.button);

            this._bindFormResetHandler();
        },
        _initButton: function () {
            var $button = this.element
                .children('.ui-category-button');

            // Если нету кнопки добавить
            if ($button.length === 0) {
                this.button = this._renderButton().appendTo(this.element);
            }
            else {
                this.button = $button;
            }

            this._on(this.button, {'click': '_handleButtonClick'});
        },
        _initTags: function () {
            var $tags = this.element.children('.ui-tags');
            // Если нету тэгов
            if ($tags.length === 0) {
                this.tags = this._renderTags().appendTo(this.element);
            }
            else {
                this.tags = $tags;
            }
            this._on($tags.tagsCategory(), {
                'tagscategoryaddtag': '_handleAddTag',
                'tagscategoryremovetag': '_handleRemoveTag'
            });
        },
        _initMenu: function () {
            var $menu = this.element.children('.ui-category-menu'),
                $list = $menu.children('.ui-category-menu-list'),
                self = this;
            this.element.css({position: 'relative'});

            if ($menu.length === 0) {
                this.menu = this._renderMenu().appendTo(this.element);
            }
            else {
                this.menu = $menu;
            }

            if ($list.length === 0) {
                this.list = this._renderMenuList().appendTo(this.menu);
            }
            else {
                this.list = $list;
            }

            this.menu.css({top: 52});

            this._on(this.menu, {
                'click .ui-category-menu-list-item': '_handleMenuListItemClick',
                'click .ui-category-menu-button': '_handleMenuButtonClick'
            });
        },
        _destroy: function () {
            this._off(this.button, 'click');
            this._off(this.menu, 'click');
            this._off(this.tags, 'tagscategoryaddtag');
            this._off(this.tags, 'tagscategoryremovetag');
            this._unbindFormResetHandler();
        },
        refresh: function () {
            this.tags.tagsCategory('refresh');
        },
        _handleButtonClick: function (event) {
            if (this.menu.is(':visible') === false) {
                this._showElement(this.menu, event);
            }
            event.preventDefault();
        },
        _handleMenuButtonClick: function (event) {
            var $button = $(event.currentTarget),
                $items = this.list
                    .children()
                    .eq(4)
                    .nextAll(),
                stateClassName = 'ui-category-menu-button-active';
            if ($button.hasClass(stateClassName) !== true) {
                $items.fadeIn();
                $button
                    .addClass(stateClassName);
            }
            else {
                $items.fadeOut();
                $button
                    .removeClass(stateClassName);
            }
            event.preventDefault();
        },
        _handleAddTag: function (event, ui) {
            this.list
                .children('.ui-category-menu-list-item')
                .filter('[data-value="' + ui.value + '"]')
                .addClass('ui-category-menu-list-item-active');
            this._trigger('addCategory', null, {id: parseInt(ui.value), text: ui.label});
            this.option('itemCount', ++this.options.itemCount);
        },
        _handleRemoveTag: function (event, ui) {
            this.list
                .children('.ui-category-menu-list-item')
                .filter('[data-value="' + ui.value + '"]')
                .removeClass('ui-category-menu-list-item-active');
            this._trigger('removeCategory', null, {id: parseInt(ui.value), text: ui.label});
            this.option('itemCount', --this.options.itemCount);
        },
        _handleMenuListItemClick: function (event) {

            var $item = $(event.currentTarget),
                label = $item.text(),
                value = $item.data('value');

            if (this.tags.tagsCategory('indexOfTag', label, value) < 0) {
                this.tags.tagsCategory('addTag', {label: label, value: value.toString()});
            }
            else {
                this.tags.tagsCategory('removeTagByLabel', label);
            }
            // Для того чтобы снять обработчик и закрыть меню
            $('body').trigger('click');
        },
        _renderButton: function () {
            var $button = $('<a/>', {
                'role': 'button',
                'href': '#'
            });
            this._addClass($button, 'ui-category-button');
            return $button;
        },
        _renderMenu: function () {
            var $menu = $('<div/>'),
                $button = this._renderMenuButton(),
                $list = this._renderMenuList();
            this._addClass($menu, 'ui-category-menu');
            return $menu.append($button, $list);
        },
        _renderMenuButton: function () {
            var $button = $('<a/>', {
                'role': 'button',
                'href': '#'
            });
            this._addClass($button, 'ui-category-menu-button');
            return $button;
        },
        _renderMenuList: function (items) {
            var $list = $('<ul/>');
            for (var i = 0; i < items.length; i++) {
                this._renderMenuListItem(items[i]).appendTo($list);
            }
            this._addClass($list, 'ui-category-menu-list');
            return $list;
        },
        _renderMenuListItem: function (item) {
            var $li = $('<li/>');
            this._addClass($li, 'ui-category-menu-list-item');
            return $li
                .attr('data-value', item.value)
                .text(item.label);
        },
        _renderTags: function () {
            return $('<div/>', {'class': 'ui-tags'});
        },
        _setOption: function (key, val) {
            if (key === 'itemCount') {
                if (val === 0) {
                    this._removeClass(this.button, 'ui-category-button-changed');
                } else if (val === 1) {
                    this._addClass(this.button, 'ui-category-button-changed');
                } else if (val === 3) {
                    this.button.hide();
                } else {
                    this.button.show();
                }
            }
            return this._super(key, val);
        }
    }]);
});