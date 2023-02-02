define(['jquery','jquery-ui','jsrender'],function($){
    return $.widget('ui.productCategory',{
        options : {
            items: [],
            button : null,
            list : null,
            hidden : null,
            plus : null,
            tips: null,
            tabs : null,
            open : false,
            content : null,
            count : 0,
            max : 3,
            active : 0,
            show : 'fadeIn',
            hide : 'fadeOut',
            duration : 150
        },
        _create : function () {
            var $root = this.element;
            this.options.button = $('.pick-add', $root);
            this.options.list = $('.pick-items', $root);
            this.options.hidden = $('input[type="hidden"]', $root);
            this.options.plus = $('.pick-plus', $root);
            this.options.tips = $('.tips', $root);
            this.options.tabs = $('.product-category-tabs', $root);
            this.options.content = $('.product-category-content', $root);
            this._on($root, {'click a': this._handleClick});
        },
        _init : function() {
            var $hidden = this.option('hidden'),
                value = $hidden.val(),
                array = value && JSON.parse(value),
                $tips = this.option('tips'),
                $self = this;
            if(array.length) {
                $.each(array, function(i, id) {
                    var $item = $tips.find('[data-id="' + id + '"]'),
                        text = $.trim($item.text());
                    $self._addItem(id,text);
                });
            }
        },
        _handleClick : function (e) {
            var $el = $(e.currentTarget),
                action = $el.data('action'),
                max = this.option('max'),
                count = this.option('count');
            if(action == 'add') {
                if(count < max) {
                    var id = $el.data('id'),
                        text = $.trim($el.text());
                    if(!$el.hasClass('is-selected')) {
                        $el.addClass('is-selected');
                        this._addItem(id,text);

                    } else {
                        $el.removeClass('is-selected');
                        this._removeItem(id);
                    }
                }
            } else if(action == 'open') {
                this.option('open',true);
            } else if(action =='del') {
                this._removeItem($el.data('id'));
            } else if(action == 'tab') {
                this.option('active', $el.parent().index());
            }
            return false;
        },
        add : function(data) {
            var $tabs = this.option('tabs'),
                count = this.option('count'),
                $content = this.option('content'),
                active = this.option('active'),
                length = $tabs.find('li').length,
                model = {
                    id : data.id,
                    text : data.text,
                    active : (!length) ? 'class="is-active"' : '',
                    size : (!length) ? 'lg' : 'sm'
                },
                template = $.templates(
                    '<li {{:active}}>' +
                        '<a data-action="tab" role="button" href="#" data-id="{{:id}}">' +
                            '<i class="icon icon-category-sm-{{:id}}"></i>' +
                            '<b class="icon icon-category-lg-{{:id}}"></b>' +
                            '<span>{{:text}}</span>' +
                        '</a>' +
                    '</li>');
            $tabs.append(template.render(model));
            $.ajax({url: '/api/activity/get-by-activity', type: 'POST', data: {
                query: data.id
            }})
            .done(function(response){
                var $list = $('<ul/>');
                if(response.data.length) {
                    $.each(response.data, function(i, item) {
                        var $li = $('<li/>').appendTo($list);
                        $('<a/>', {
                            'text' : item.title,
                            'data-id' : item.id,
                            'data-action' : 'add'
                        }).appendTo($li);
                        $content.append($list);
                    });
                } else {
                    //do nothing
                }
            });
        },
        del : function(id) {
            var $tabs = this.option('tabs'),
                $content = this.option('content'),
                $target = $tabs.find('[data-id="' + id + '"]'),
                index = $target.index();
            $target.parents('li')
                .remove();
            $content.find('ul')
                .eq(index)
                .remove();
        },
        _addItem : function (id, text) {
            var items = this.option('items'),
                $plus = this.option('plus'),
                model = {
                    id : id,
                    text : text
                },
                template = $.templates('' +
                    '<li>' +
                        '<i class="icon icon-category-sm-{{:id}}"></i>' +
                        '<span>{{:text}}</span>' +
                        '<a href="#" role="button" data-action="del" data-id="{{:id}}">' +
                            '<i class="icon icon-close"></i>' +
                        '</a>' +
                    '</li>');
            $(template.render(model)).insertBefore($plus.parent());
            items.push(id);
            this.option('items', items);
            this._trigger('add', null, {id : id, text: text});
        },
        _removeItem : function (id) {
            var items = this.option('items'),
                $list = this.option('list'),
                $tips = this.option('tips'),
                query = '[data-id="'+id+'"]';
            $tips.find(query).removeClass('is-selected');
            $list.find(query).closest('li').remove();
            items.splice(items.indexOf(id),1);
            this.option('items',items);
            this._trigger('del', null, id);
        },
        _setOption : function (key,val) {
            var $tips = this.option('tips'),
                $button = this.option('button'),
                $list = this.option('list'),
                $plus = this.option('plus'),
                $hidden = this.option('hidden'),
                $tabs = this.option('tabs'),
                $content = this.option('content'),
                $self = this,
                show = this.option('show'),
                hide = this.option('hide'),
                duration = this.option('duration');
            if(key == 'open') {
                if(val) {
                    $('body').on('click', function(e){
                        var $el = $(e.target),
                            $parent = $tips.parent();
                        if($parent.is($el) || $parent.has($el).length)  {
                            return false;
                        } else {
                            $self.option('open', false);
                            $('body').off(e);
                        }
                    });
                    $tips.animate({marginTop : 10},{duration : duration, queue : false});
                    this._show($tips, {effect : show, duration : duration});
                } else {
                    $tips.animate({marginTop : 15},{duration : duration, queue : false});
                    this._hide($tips, { effect : hide, duration : duration});
                }
            } else if (key == 'items') {
                $hidden.val(JSON.stringify(val));
                if(val.length >= 3) {
                    $plus.hide();
                } else {
                    $plus.show();
                }
                if(val.length) {
                    $button.hide();
                    $list.show();
                } else {
                    $button.show();
                    $list.hide();
                }
                this.option({
                    count : val.length,
                    open : false
                });
            } else if (key == 'active') {
                $tabs.find('li')
                    .removeClass('is-active')
                    .eq(val)
                    .addClass('is-active');
                $content.find('ul')
                    .hide()
                    .eq(val)
                    .show();
            }
            return this._super(key,val);
        }
    });
});