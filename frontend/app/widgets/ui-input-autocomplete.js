/**
 * Собственный виджет для автозаполнения.
 * События:
 * @event elementSelected - вызывается при нажатии по элементу меню.
 * @version beta
 * @author Артём Широких kowapssupport@gmail.com
 */
export default $.widget('ui.search', {
    options: {
        // URL для обработки запроса.
        url: null,
        // тип запроса.
        request_type: 'POST',
        // интервал времени в мс при вводе поисковой фразы.
        change_interval: 500,
        // название параметра для поиска в запросе.
        query_param: 'query',
        // включить / отключить живой поиск запросом на сервер.
        live_search_enabled: true,
        // данные для поиска.
        local_data: [],
        // автопоказ меню при инициализации.
        auto_open: false,
    },
    // объект запроса.
    xhr: null,
    _create: function () {
        let self_object = this;

        // оборачиваю элемент.
        if (this.element.parents('div.search-wrapper').length == 0) {
            this.element.wrap(function() {
                return '<div class="search-wrapper" />';
            });
        }

        // автопоказ меню.
        if (this.options.auto_open
            && !this.options.live_search_enabled
            && $.isArray(this.options.local_data)
            && this.options.local_data.length > 0
        ) {
            // отображаю меню.
            this.renderMenu(
                this.options.local_data.slice(0, 5)
            );
        }

        // проверяю, в случае если при инициализации есть меню.
        let menu = this.getMenu();
        if (menu.length && !this.options.auto_open) {
            menu.remove();
        }

        // отображение подсказки при нажатии на input.
        this.element.click(function () {
            //noinspection JSUnresolvedFunction
            let menu = self_object.getMenu();
            if (menu.length && menu.find('li').length) {
                self_object.showMenu();
            }
        });

        // @see https://stackoverflow.com/a/5917358
        this.element.bind("input propertychange", function () {
            let th = $(this);

            // проверка на изменение только свойства value;
            if (window.event && event.type == "propertychange" && event.propertyName != "value") {
                return false;
            }

            // очистка старого значения.
            self_object.element.attr('data-value', '');

            if (self_object.options.live_search_enabled == false) {
                // поиск в локальных данных.
                //noinspection JSUnresolvedFunction
                self_object.searchLocal(th.val());
            }
            else {
                // очистка старого Timeout;
                window.clearTimeout($(this).data("timeout"));
                $(this).data("timeout", setTimeout(function () {
                    self_object.search(th.val());
                }, self_object.options.change_interval));
            }
        });

        // обработка нажатия на найденный элемент поиска.
        $('body').on('click', this.getMenu().find('li').selector, function (e) {
            e.preventDefault();
            //noinspection JSUnresolvedFunction
            self_object.handleMenuItemClick($(this));
            return false;
        });

        // скрытие блока с подсказками при клике на пустое место.
        $(document).click(function (e) {
            //noinspection JSUnresolvedFunction
            let hint_menu = self_object.getMenu();
            if (hint_menu.is(':visible')) {
                let div = $(hint_menu.selector);
                if (!div.is(e.target)
                    && !self_object.element.is(e.target)
                    && div.has(e.target).length === 0) {
                    div.hide();
                }
            }
        });
    },
    clear: function () {
        let $menu = this.getMenu();
        if ($menu.length) {
            $menu.find('li').remove();
        }
    },
    getMenu: function () {
        return this.element.parents('div').find('.ui-menu[data-uuid="'+this.uuid+'"]');
    },
    prepareQuery: function (query) {
        query = query.replace(/\s\s+/g, ' ');
        query = query.trim();
        return query;
    },
    /**
     * Поиск в локальных данных local_data
     * @param query
     */
    searchLocal: function (query) {
        if (!$.isArray(this.options.local_data)) {
            return false;
        }
        if (this.options.local_data.length == 0) {
            return false;
        }
        query = this.prepareQuery(query);
        let filtered = this.options.local_data.filter(function (element) {
            if (!query.length) {
                return false;
            }
            if (!element.hasOwnProperty('label')) {
                return false;
            }
            let label = element.label.toLowerCase();
            return (label.indexOf(query.toLowerCase()) !== -1);
        });
        this.renderMenu(filtered);
    },
    /**
     * Выполняет запрос и вызывает afterSearch;
     * @param query
     * @returns {boolean}
     */
    search: function (query) {
        query = this.prepareQuery(query);
        let self_object = this;
        if(this.xhr && this.xhr.readyState != 4) {
            // в случае если предъидущий запрос еще не выполнился - убиваю.
            this.xhr.abort();
        }
        if (!query) {
            this.hideMenu();
            return false;
        }
        let query_data = {};
        query_data[this.options.query_param] = query;
        this.xhr = $.ajax({url: this.options.url, type: this.options.request_type, data: query_data})
            .done(function(response) {
                self_object.afterSearch(response);
            });
    },
    /**
     * Обработчик нажатия на найденный элемент подсказки.
     * @param element
     */
    handleMenuItemClick: function (element) {
        let span = element.find('span:last');
        let text = span.text().trim();
        let value = span.data('value');
        let menu = this.getMenu();
        this.element.val(text);
        this.element.attr('data-value', value);
        this.hideMenu();
        this.element.trigger('elementSelected', { value: value, text: text });
        //menu.find('li').remove();
    },
    afterSearch: function (response) {
        // ожидаем в response свойство data с массивом данных [label, value];
        if (response.data == undefined || !response.data || response.data.length == 0) {
            return false;
        }
        this.renderMenu(response.data);
    },
    /**
     * Метод рендерит подсказку.
     */
    renderItem: function (item) {
        return this.getMenuItemTemplate(item.value, item.label);
    },
    /**
     * Метод рендерит меню со списком подсказок.
     * @param items [label, value]
     */
    renderMenu: function (items) {
        let self_object = this
            ,items_html = [];
        if (!$.isArray(items)) {
            return false;
        }
        // пустой массив - закрываю меню.
        if (!items.length) {
            // todo - имеет смысл удалять элементы.
            this.hideMenu();
            return false;
        }
        items.forEach(function (item) {
            //noinspection JSUnresolvedFunction
            items_html.push(self_object.renderItem(item));
        });
        items_html = items_html.join('');
        // если у элемента уже есть меню - обновляю элементы.
        let found_menu = this.getMenu();
        if (found_menu.length) {
            found_menu.find('li').html('');
            found_menu.html(items_html);
        }
        else {
            // первое создание меню.
            $(this.getMenuTemplate(items_html))
                .insertAfter(this.element);
            // первый рендеринг меню.
            this.resizeMenu();
        }
        this.showMenu();
    },
    resizeMenu: function () {
        let found_menu = this.getMenu();
        found_menu.width(this.element.width());
    },
    /**
     * Отображение меню.
     */
    showMenu: function () {
        let found_menu = this.getMenu();
        if (found_menu.length) {
            found_menu.show();
        }
    },
    /**
     * Скрытие меню.
     */
    hideMenu: function () {
        let found_menu = this.getMenu();
        if (found_menu.length) {
            found_menu.hide();
        }
    },
    /**
     * todo: вынести классы в настройки.
     * Возвращает шаблон меню.
     * @param items_html
     * @returns {string}
     */
    getMenuTemplate: function (items_html) {
        return '<ul data-uuid="'+this.uuid+'" tabindex="0" style="display: none;" class="ui-menu ui-widget ui-widget-content ui-autocomplete input-menu ui-front">' + items_html + '</ul>';
    },
    /**
     * Возвращает шаблон элемента меню.
     * todo: вынести классы в настройки.
     * @param value
     * @param label
     * @returns {string}
     */
    getMenuItemTemplate: function (value, label) {
        return '<li class="ui-autocomplete-item input-menu-item ui-menu-item">' +
                    '<div class="ui-autocomplete-wrapper ui-menu-item-wrapper" tabindex="-1">' +
                        '<span class="hint-item" data-value="'+value+'">'+label+'</span>' +
                        //'<strong class="hint-item ui-autocomplete-highlight" data-value="'+value+'">'+label+'</strong>' +
                    '</div>' +
                '</li>';
    },
    _setOption: function (key, val) {
        return this._super(key, val);
    },
    _destroy: function () {
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        this._off(this.element, 'click');
        let menu = this.getMenu();
        if (menu.length) {
            menu.remove();
        }
    },
});