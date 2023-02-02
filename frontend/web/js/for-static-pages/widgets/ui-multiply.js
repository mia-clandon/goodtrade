define(['jquery', 'jquery-ui'], function ($) {
    return $.widget('ui.multiply', {
        options: {
            count: 1,
            max: 20,
            separateAddBtn: false
        },
        _create: function () {
            let $root = this.element;
            this._on($root, {'click a': this._handleClick});
        },
        _init: function () {
            let $root = this.element,
                $li = $root.children('li'),
                //$instance = $li.children(':first'),
                count = $li.length;
            this._trigger('init', null, $root);
            this.option({count: count});
        },
        add: function () {
            let $root = this.element,
                $first = $root.children('li:first'),
                $instance = $first.children(':first'),
                currentCount = this.option('count'),
                maxCount = this.option('max'),
                $newInstance = $instance.clone();

            if (currentCount >= maxCount) {
                return false;
            }

            // если input со значение внутри клонируемого блока.
            $newInstance
                .find('input').val('').attr("value", "")
                .end().find('.label').remove();
            // в случае если клонируется сам input.
            $newInstance
                .val('').attr("value", "")
                .parent().find('.label').remove();

            let $newItem = $('<li/>', {class: $first.attr('class')})
                .hide()
                .append($newInstance);

            // Если ссылка, отвечающая за добавление элементов, находится в отдельном, последнем li
            if (this.options.separateAddBtn) {
                $newItem.insertBefore($root.find("li:last")).slideDown("fast");
            }
            else {
                $newItem.appendTo($root).slideDown("fast");
            }

            $('<a/>', {
                'role': 'button',
                'href': '#',
                'data-action': 'del'
            }).appendTo($newItem);

            this.element.trigger('add', {new_instance: $newInstance, new_item: $newItem});
            this.option('count', ++currentCount);

            if (this.options.separateAddBtn && currentCount === maxCount) {
                $root.children("li:last").slideUp("fast");
            }
        },
        del: function (index) {
            let $root = this.element,
                $li = $root.children('li'),
                currentCount = this.option('count'),
                maxCount = this.option('max');

            if (currentCount !== 1) {
                $li.eq(index).slideUp('fast', function () {
                    $(this).remove();
                });
            }
            this.element.trigger('del', {});
            this.option('count', --currentCount);

            if (this.options.separateAddBtn && currentCount === maxCount - 1) {
                $root.children("li:last").slideDown("fast");
            }
        },
        _handleClick: function (e) {
            let $el = $(e.target),
                action = $el.data('action'),
                index = $el.closest('li').index();
            if (action === 'add') {
                this.add();
            }
            else if (action === 'del') {
                this.del(index);
            }
            e.preventDefault();
        }
    });
});