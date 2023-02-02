define(['jquery', 'jquery-ui', 'jsrender'], function ($) {
    /**
     * TODO: не понятно где и как используется.
     */
    return $.widget('ui.pickCategory', {
        options: {
            items: [],
            button: null,
            list: null,
            hidden: null,
            plus: null,
            tips: null,
            open: false,
            max: 3,
            count: 0,
            show: 'fadeIn',
            hide: 'fadeOut',
            duration: 150
        },
        _create: function () {
            var $root = this.element;
            this.options.button = $root.find('.pick-add');
            this.options.list = $root.find('.pick-items');
            this.options.hidden = $root.find('input[type="hidden"]');
            this.options.plus = $root.find('.pick-plus');
            this.options.tips = $root.find('.tips');
            this._on($root, {'click a': this._handleClick});
        },
        _init: function () {
            var $tips = this.option('tips'),
                $hidden = this.option('hidden'),
                value = $hidden.val(),
                array = value && JSON.parse(value),
                $self = this;
            if (array.length) {
                $.each(array, function (i, id) {
                    var $item = $tips.find('[data-id="' + id + '"]'),
                        text = $.trim($item.text());
                    $self._addItem(id, text);
                });
            }
        },
        _handleClick: function (e) {
            var $el = $(e.currentTarget),
                action = $el.data('action'),
                max = this.option('max'),
                count = this.option('count');
            if (action == 'add') {
                if (count < max) {
                    var id = $el.data('id'),
                        text = $.trim($el.text());
                    if (!$el.hasClass('is-selected')) {
                        $el.addClass('is-selected');
                        this._addItem(id, text);

                    } else {
                        $el.removeClass('is-selected');
                        this._removeItem(id);
                    }
                }
            } else if (action == 'open') {
                this.option('open', true);
            } else if (action == 'del') {
                this._removeItem($el.data('id'));
            }
            e.preventDefault();
        },
        _addItem: function (id, text) {
            var items = this.option('items'),
                $plus = this.option('plus'),
                model = {
                    id: id,
                    text: text
                },
                template = $.templates('<li>' +
                    '<i class="icon icon-category-sm-{{:id}}"></i>' +
                    '<span>{{:text}}</span>' +
                    '<a href="#" role="button" data-action="del" data-id="{{:id}}">' +
                    '<i class="icon icon-close"></i>' +
                    '</a>' +
                    '</li>');
            $(template.render(model)).insertBefore($plus.parent());
            items.push(id);
            this.option('items', items);
            this._trigger('add', null, {id: id, text: text});
        },
        _removeItem: function (id) {
            var items = this.option('items'),
                $list = this.option('list'),
                $tips = this.option('tips'),
                query = '[data-id="' + id + '"]';
            $tips.find(query).removeClass('is-selected');
            $list.find(query).closest('li').remove();
            items.splice(items.indexOf(id), 1);
            this.option('items', items);
            this._trigger('del', null, id);
        },
        _setOption: function (key, val) {
            var $button = this.option('button'),
                $list = this.option('list'),
                $plus = this.option('plus'),
                $hidden = this.option('hidden'),
                $tips = this.option('tips'),
                $self = this,
                show = this.option('show'),
                hide = this.option('hide'),
                duration = this.option('duration');
            if (key == 'items') {
                $hidden.val(JSON.stringify(val));
                if (val.length >= 3) {
                    $plus.hide();
                } else {
                    $plus.show();
                }
                if (val.length) {
                    $button.hide();
                    $list.show();
                } else {
                    $button.show();
                    $list.hide();
                }
                this.option({
                    count: val.length,
                    open: false
                });
            } else if (key == 'open') {
                if (val) {
                    $('body').on('click', function (e) {
                        var $el = $(e.target),
                            $parent = $tips.parent();
                        if ($parent.is($el) || $parent.has($el).length) {
                            return false;
                        } else {
                            $self.option('open', false);
                            $('body').off(e);
                        }
                    });
                    $tips.animate({marginTop: 10}, {duration: duration, queue: false});
                    this._show($tips, {effect: show, duration: duration});

                } else {
                    $tips.animate({marginTop: 15}, {duration: duration, queue: false});
                    this._hide($tips, {effect: hide, duration: duration});
                }
            }
            return this._super(key, val);
        }

    });
});