define( 'widgets/ui-multiply-b2b',['jquery', 'jquery-ui'], function ($) {
    return $.widget('ui.multiply', {
        options: {
            count: 1,
            max: 3
        },
        _create: function () {
            let $root = this.element;
            this._on($root, {'click a': this._handleClick});
        },
        _init: function () {
            let $root = this.element,
                $li = $root.children('li'),
                count = $li.length;

            this._trigger('init', null, $root);
            this.option({ count: count });
        },
        // Клонирование элемента
        add: function () {
            let $root = this.element,
                $first = $root.children('li:first'),
                $instance = $first.children(':first'),
                currentCount = this.option('count'),
                maxCount = this.option('max'),
                $newInstance = $instance.clone(),
                $addBtn = $root.find('[data-action="add"]');

            if (currentCount >= maxCount) {
                return false;
            }

            // если input со значением внутри клонируемого блока.
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

            $addBtn.appendTo($newItem);

            if (currentCount + 1 === maxCount) {
                $addBtn.hide();
            }

            $newItem.appendTo($root).slideDown("fast");

            let $delButton = $('<a>', {
                class: 'button button_small button_link-red',
                'href': '#',
                'data-action': 'del'
            })
                .append($('<span>').addClass('button__text').html('Удалить'));

            let $swapButton = $('<a>', {
                class: 'button button_small button_link d-none d-lg-flex',
                'href': '#',
                'data-action': 'swap'
            }).append($('<span>').addClass('button__text').html('Сделать основным'));

            if ($newInstance.children(':last').is(".icon")) {
                $newInstance.children(':last').before($swapButton).before($delButton);
            } else {
                $newInstance.append($swapButton).append($delButton);
            }

            this.element.trigger('add', {new_instance: $newInstance, new_item: $newItem});
            this.option('count', ++currentCount);
        },
        // Удаление элемента
        del: function (index) {
            let $root = this.element,
                $li = $root.children('li'),
                currentCount = this.option('count'),
                maxCount = this.option('max'),
                $addBtn = $root.find('[data-action="add"]');

            if (currentCount !== 1) {
                $li.eq(index).slideUp('fast', function () {
                    if (index === $li.length - 1) {
                        $addBtn.appendTo($(this).prev());
                    }
                    $(this).remove();
                });
            if (currentCount === maxCount) {
                $addBtn.show();
            }
            }
            this.element.trigger('del', {});
            this.option('count', --currentCount);
        },
        // Смена элементов местами
        swap: function (index) {
            let $root = this.element,
                $li = $root.children('li'),
                $firstLi = $li.eq(0),
                $swapLi = $li.eq(index);

            $swapLi.add($firstLi).slideUp('fast');

            $swapLi.after($firstLi);
            $swapLi.parent().prepend($swapLi);

            if ($swapLi.children(".input").children(':last').is(".icon")) {
                $swapLi.children(".input").children(':last').before($firstLi.find(".label"));
                $firstLi.children(".input").children(':last').before($swapLi.find(".button"));
            } else {
                $swapLi.children(".input").append($firstLi.find(".label"));
                $firstLi.children(".input").append($swapLi.find(".button"));
            }

            if (index === $li.length - 1) {
                $swapLi.children(":last").appendTo($firstLi);
            }

            $swapLi.add($firstLi).slideDown('fast');
            this.element.trigger('swap', {});
        },
        _handleClick: function (e) {
            let $el = $(e.currentTarget),
                action = $el.data('action'),
                index = $el.closest('li').index();
            if (action === 'add') {
                this.add();
            }
            else if (action === 'del') {
                this.del(index);
            }
            else if (action === 'swap') {
                this.swap(index);
            }
            e.preventDefault();
        }
    });
});