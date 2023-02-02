/**
 * Новый контрол для категорий. (сферы деятельности, категории товаров.)
 * @author Артём Широких kowapssupport@gmail.com
 */

import './ui-input-tags';

export default $.widget('ui.category', {
    options: {
        // количество допустимых категорий / сфер деятельностей.
        max_tag_limit: 3,
        // выбранные категории контрола (из PHP).
        control_selected_values: [],

        // #runtime.
        // выбранные сферы деятельности.
        selected_activities: [],
        // выбранные категории товаров.
        selected_product_categories: [],

        // состояние когда необходимо подгружать категории по сфере деятельности.
        is_product_category_state: false,

        // сферы деятельностей организации.
        firm_activities: [],
    },

    // всплывающее меню со сферами деятельностей.
    activity_menu: null,
    // всплывающее меню с категориями товаров.
    product_category_menu: null,

    // блок с тегами категорий.
    tags_block: null,
    // открыт ли блок с дополнительными категориями.
    all_category_list_is_shown: false,

    // идет ли загрузка категорий по сфере деятельности в данный момент.
    is_product_category_loading: false,

    // загруженные ajax'ом категории сфер деятельностей.
    ajax_activity_product_category_loaded_list: [],

    _create: function () {},
    _init: function () {
        // инициализация контрола тегов.
        this.tags_block = this.element.find('.ui-tags');
        if (this.tags_block.length > 0) {
            this.tags_block.inputTags();
        }

        // выбранные категории контрола.
        this.options.control_selected_values = this.element.data('selected-values');

        // меню со сферами деятельности.
        this.activity_menu = this.element.find('div.ui-category-menu');
        // меню с категориями товаров.
        this.product_category_menu = this.element.find('.ui-product-menu-wrap');

        // сферы деятельностей организации.
        this.options.firm_activities = this.element.data('firm-activities-ids');

        // состояние когда необходимо подгружать категории по сфере деятельности.
        this.options.is_product_category_state
            = this.element.data('is-product-category-state') === 1;

        // блок с тегами.
        this.options.tags_block = this.element.find('.ui-tags');

        this._bindEvents();
    },
    _bindEvents: function () {
        let self_object = this;

        // обработчик нажатия на кнопку выбора категории / сферы деятельности.
        this.element.find('a.ui-category-button').on('click', function (e) {
            e.preventDefault();
            if (self_object.options.is_product_category_state) {

                if (self_object.options.firm_activities.length === 0) {
                    self_object.toggleActivityMenu();
                }
                else {
                    // меню категорий товаров.
                    self_object.toggleProductCategoryMenu();
                }
            }
            else {
                // меню сфер деятельностей.
                self_object.toggleActivityMenu();
            }
            return false;
        });

        // обработчик нажатия на сферу деятельности.
        this.element.find('li.ui-category-menu-list-item').on('click', function (e) {
            e.preventDefault();
            let activity_id = parseInt($(this).data('value'));
            self_object.handleActivityItemClick(activity_id, $(this));
            return false;
        });

        // обработчик нажатия на категорию товара.
        this.element.on('click', 'li.ui-product-submenu-item, li.ui-product-submenu-submenu-item', function (e) {
            e.preventDefault();
            if ($(this).hasClass('with-submenu')) {
                return false;
            }
            let $span = $(this).find('span:first');
            self_object.handleProductCategoryItemClick(parseInt($span.data('id')), $(this));
            return false;
        });

        // открытие / закрытие меню дополнительных категорий.
        this.element.on('click', 'a.ui-category-menu-button', function (e) {
            e.preventDefault();
            self_object.toggleCategories($(this));
            return false;
        });

        // скрытие блока с категориями при клике на пустое место.
        $(document).click(function (e) {
            if (self_object.activity_menu.css('display') === 'block') {
                let div = $(self_object.activity_menu);
                if (!div.is(e.target)
                    && div.has(e.target).length === 0) {
                    div.hide();
                }
            }
            if (self_object.product_category_menu.css('display') === 'block') {
                let div2 = $(self_object.product_category_menu);
                if (!div2.is(e.target)
                    && div2.has(e.target).length === 0) {
                    div2.hide();
                }
            }
        });

        // удаление тега по крестику.
        this.tags_block.on('tagRemoved', function (e, data) {
            let tag_id = parseInt(data.value);
            if (self_object.options.is_product_category_state) {
                // удаление категории товара.
                self_object.removeCategoryTag(tag_id);
            }
            else {
                // удаление сферы деятельности товара.
                self_object.removeActivityTag(tag_id);
            }
        });

        // переключение между сферами деятельности в меню.
        this.element.on('click', '.ui-product-menu-item', function (e) {
            e.preventDefault();
            self_object.toggleProductCategory($(this));
            // задаю высоту меню - костыль стилей
            self_object.setMenuHeight($(this));
            return false;
        });

        // отображение вложенных категорий товара.
        this.element.on('click', '.ui-product-submenu-item.with-submenu', function (e) {
            e.preventDefault();
            self_object.toggleSubProductCategory($(this));
            // задаю высоту меню - костыль стилей
            self_object.setMenuHeight($(this));
            return false;
        });

        // возврат к субменю сфер деятельностей.
        this.element.on('click', 'a.ui-product-menu-back.submenu', function (e) {
            e.preventDefault();
            self_object.hideSubCategories($(this));
            // задаю высоту меню - костыль стилей
            self_object.setMenuHeight($(this).prev("li"));
            return false;
        });

        // возврат к сферам деятельности.
        this.element.on('click', 'a.ui-product-menu-back.all', function (e) {
            e.preventDefault();
            self_object.hideProductCategoryMenu();
            self_object.showActivityMenu();
            return false;
        });
    },

    /**
     * Переключение между сферами деятельности.
     * @param $category
     * @returns {ui.category}
     */
    toggleProductCategory: function ($category) {
        $(this.product_category_menu.selector + ' li.ui-product-menu-item-active')
            .removeClass('ui-product-menu-item-active');
        $category.addClass('ui-product-menu-item-active');
        return this;
    },

    /**
     * Отображение / скрытие вложенных категорий.
     * @param $category
     */
    toggleSubProductCategory: function ($category) {
        if ($category.hasClass('submenu-is-open')) {
            $category.parents('ul:first').removeClass('second-level-submenu-is-open');
            $category.removeClass('submenu-is-open');
        }
        else {
            $category.parents('ul:first').addClass('second-level-submenu-is-open');
            $category.addClass('submenu-is-open');
        }
    },
    /**
     * Скрытие открытых подкатегорий.
     * @param $element
     */
    hideSubCategories: function ($element) {
        let $ul = $element.parents('ul:first');
        $ul.removeClass('second-level-submenu-is-open');
        $ul.find('li.submenu-is-open').removeClass('submenu-is-open');
    },

    /**
     * Обработчик нажатия на элемент сферы деятельности.
     * @param activity_id
     * @param $element
     */
    handleActivityItemClick: function (activity_id, $element) {

        // подгрузка категорий по сфере деятельности.
        if (this.options.is_product_category_state) {
            this.handleLoadActivityCategories(activity_id);
            return false;
        }

        // сфера деятельности уже выбрана.
        if ($.inArray(activity_id, this.options.selected_activities) > -1
            || $.inArray(activity_id, this.options.control_selected_values) > -1
        ) {
            // удаление тега.
            this.tags_block.inputTags("removeTagByValue", activity_id);
            this.removeActivityTag(activity_id, $element);
            // триггерю событие добавление тега сферы деятельности.
            this.element.trigger('activityRemoved', {value: activity_id});
        }
        else {
            // добавление нового тега (сфера деятельности).
            this.addActivityTag({
                label: $element.text().trim(),
                icon_class: $element.data('icon'),
                value: $element.data('value')
            }, $element);
        }
    },

    /**
     * Обработчик нажатия на элемент категории.
     * @param category_id
     * @param $element
     */
    handleProductCategoryItemClick: function (category_id, $element) {

        let $span = $element.find('span:first')
            ,activity_id = parseInt($span.data('activity-id'));

        // категория уже выбрана.
        if ($.inArray(category_id, this.options.selected_activities) > -1
            || $.inArray(category_id, this.options.selected_product_categories) > -1
        ) {
            // удаление тега.
            this.tags_block.inputTags("removeTagByValue", category_id);
            this.removeCategoryTag(category_id, activity_id, $element);
        }
        else {
            // добавление нового тега (категория товара).
            this.addCategoryTag({
                label: $span.text().trim(),
                icon_class: '',
                value: category_id,
                activity_id: activity_id
            }, $element);
        }
    },

    /**
     * Обработчик загрузки категорий по сфере деятельности.
     * @param activity_id
     */
    handleLoadActivityCategories: function (activity_id) {
        let self_object = this;

        if (!this.is_product_category_loading) {
            this.is_product_category_loading = true;

            // уже загружены категории сферы деятельности.
            if ($.inArray(activity_id.toString(), this.options.firm_activities) > -1) {
                this.hideActivityMenu();
                this.showProductCategoryMenu();
                this.is_product_category_loading = false;
                return false;
            }

            this.setLoaderState(activity_id);

            // загрузка категорий.
            this.loadProductCategoryListByCategory(activity_id, function (response_html) {

                self_object.hideActivityMenu();
                self_object.showProductCategoryMenu();

                self_object.setNotLoaderState(activity_id);

                // убираю класс .ui-product-menu-item-active у остальных категорий.
                $(self_object.product_category_menu.selector + ' li.ui-product-menu-item-active')
                    .removeClass('ui-product-menu-item-active');

                // вставляю подгруженные категории товаров до ссылки.
                self_object.product_category_menu.find('.ui-product-menu').append(response_html);

                // добавляю сферу деятельности в список всех.
                self_object.options.firm_activities.push(activity_id.toString());

                // категории загружены.
                self_object.is_product_category_loading = false;

                // задаю высоту меню - костыль стилей
                self_object.setMenuHeight(self_object.product_category_menu.find('.ui-product-menu-item-active'));
            });
        }
    },

    /**
     * Загрузка списка дочерних категорий.
     * @param category_id
     * @param callback
     */
    loadProductCategoryListByCategory: function (category_id, callback) {
        $.ajax({url: '/api/activity/get-control-category-list', type: 'POST', data: {category_id: category_id}})
        .done(function (response) {
            callback(response);
        })
        .fail(function () {
            // что то пошло не так - сервер ругается.
            console.log(response.statusText);
            $.notify("Произошла ошибка, попробуйте позже !", "error");
        });
    },

    /**
     * Метод проверяет, есть ли выбранные категории по сфере деятельности.
     * @return int
     */
    getCountActiveCategoriesInActivities: function (activity_id) {
        if (!this.options.is_product_category_state) {
            return 0;
        }
        return this.element.find('li.ui-product-menu-item[data-id='+activity_id+'] .ui-product-submenu li.ui-category-menu-list-item-active')
            .length
        ;
    },

    /**
     * Возвращает LI сферы деятельности по id.
     * @param activity_id
     * @return {*|jQuery|HTMLElement}
     */
    getActivityLi: function (activity_id) {
        return this.element.find('.ui-category-menu-list-item[data-value='+activity_id+']');
    },

    /**
     * Состояние загрузки сферы деятельности.
     * @param activity_id
     * @returns {ui.category}
     */
    setLoaderState: function (activity_id) {
        // состояние загрузки.
        this.getActivityLi(activity_id)
            .addClass('ui-category-menu-list-item-loading');
        return this;
    },

    /**
     * Убирает состояние загрузки сферы деятельности.
     * @param activity_id
     * @returns {ui.category}
     */
    setNotLoaderState: function (activity_id) {
        // состояние загрузки off.
        this.element.find('.ui-category-menu-list-item[data-value='+activity_id+']')
            .removeClass('ui-category-menu-list-item-loading');
        return this;
    },

    /**
     * Добавление нового тега сферы деятельности.
     * @param activity_data
     * @param $element
     * @returns {boolean}
     */
    addActivityTag: function (activity_data, $element) {
        if (!this.canCreateNewTag()) {
            return false;
        }
        let activity_id = parseInt(activity_data.value)
            ,selected_activities_index = this.options.selected_activities.indexOf(activity_id)
            ,selected_values_index = this.options.control_selected_values.indexOf(activity_id)
        ;
        // убеждаюсь что такого тега нет в выбранных категориях.
        if (selected_activities_index === -1 && selected_values_index === -1) {
            this.options.selected_activities.push(activity_id);
            $element.addClass('ui-category-menu-list-item-active');
            this.tags_block.inputTags("addTag", activity_data);

            // триггерю событие добавления тега сферы деятельности.
            this.element.trigger('activityAdded', {value: activity_id});
        }
    },

    /**
     * Добавление нового тега категории.
     * @param category_data
     * @param $element
     * @returns {boolean}
     */
    addCategoryTag: function (category_data, $element) {
        if (!this.canCreateNewTag()) {
            return false;
        }
        let category_id = parseInt(category_data.value)
            ,selected_product_category = this.options.selected_product_categories.indexOf(category_id)
            ,selected_values_index = this.options.control_selected_values.indexOf(category_id)
        ;
        // убеждаюсь что такого тега нет в выбранных категориях.
        if (selected_product_category === -1 && selected_values_index === -1) {

            this.options.selected_product_categories.push(category_id);
            $element.addClass('ui-category-menu-list-item-active');
            this.tags_block.inputTags("addTag", category_data);

            // делаю активной сферу деятельности.
            let activity_id = parseInt(category_data.activity_id);
            this.activity_menu.find('.ui-category-menu-list.activities li[data-value='+activity_id+']')
                .addClass('ui-category-menu-list-item-active');

            // триггерю событие добавления тега категории товара.
            this.element.trigger('categoryAdded', {value: category_id});
        }
    },

    /**
     * Удаление нового тега сферы деятельности.
     * @param activity_id
     * @param $element
     */
    removeActivityTag: function (activity_id, $element = null) {

        // поиск в выбранных сферах деятельностей.
        let selected_activities_index = this.options.selected_activities.indexOf(activity_id);
        if (selected_activities_index > -1) {
            this.options.selected_activities.splice(selected_activities_index, 1);
        }
        // поиск в выбранных значениях (из php).
        let selected_values_index = this.options.control_selected_values.indexOf(activity_id);
        if (selected_values_index > -1) {
            this.options.control_selected_values.splice(selected_values_index, 1);
        }

        if ($element === null) {
            $element = this.element.find('li.ui-category-menu-list-item[data-value="'+activity_id.toString()+'"]');
        }
        $element.removeClass('ui-category-menu-list-item-active');
    },

    /**
     * Удаление тега категории.
     * @param category_id
     * @param activity_id
     * @param $element
     */
    removeCategoryTag: function (category_id, activity_id = 0, $element = null) {

        // поиск в выбранных категориях.
        let selected_categories_index = this.options.selected_product_categories.indexOf(category_id);
        if (selected_categories_index > -1) {
            this.options.selected_product_categories.splice(selected_categories_index, 1);
        }
        // поиск в выбранных значениях (из php).
        let selected_values_index = this.options.control_selected_values.indexOf(category_id);
        if (selected_values_index > -1) {
            this.options.control_selected_values.splice(selected_values_index, 1);
        }

        if ($element === null) {
            $element = this.element.find('li.product-category-item span[data-id='+category_id+']');
            activity_id = parseInt($element.data('activity-id'));
            $element = $element.parent();
        }

        $element.removeClass('ui-category-menu-list-item-active');

        // убираю признак активности у сферы деятельности (если у этой сферы деятельности осталась активная только 1 категория).
        if (this.getCountActiveCategoriesInActivities(activity_id) === 0) {
            this.getActivityLi(activity_id)
                .removeClass('ui-category-menu-list-item-active');
        }

        // триггерю событие удаление тега сферы деятельности.
        this.element.trigger('categoryRemoved', {value: category_id});
    },
    
    /** может ли пользователь добавлять тег. */
    canCreateNewTag: function () {
        let selected_values = this.getSelectedIds();
        return selected_values.length < this.options.max_tag_limit;
    },

    /** возвращает выбранные значения контрола. */
    getSelectedIds: function () {
        let selected_values = this.options.control_selected_values,
            selected_activities = this.options.selected_activities,
            selected_categories = this.options.selected_product_categories,
            // все категории.
            all = selected_values.concat(selected_activities, selected_categories)
        ;
        return all.filter((v, i, a) => a.indexOf(v) === i);
    },

    /**
     * Открытие / закрытие дополнительных категорий.
     * @return {ui.category}
     */
    toggleCategories: function (element) {
        let hidden_class = 'ui-category-menu-button-hidden',
            text_class = 'ui-category-menu-button-text';

        if (this.all_category_list_is_shown) {
            // скрываю доп категории.
            element.find('span.show')
                .removeClass(hidden_class).addClass(text_class);
            element.find('span.hide')
                .removeClass(text_class).addClass(hidden_class);

            // скрытие категорий.
            this.activity_menu.find('ul.activities li.was-hidden')
                .removeClass('was-hidden')
                .hide();

            this.all_category_list_is_shown = false;
        }
        else {
            // показываю доп категории.
            element.find('span.show')
                .removeClass(text_class).addClass(hidden_class);
            element.find('span.hide')
                .removeClass(hidden_class).addClass(text_class);

            // отображение категорий.
            this.activity_menu.find('ul.activities li:hidden')
                .addClass('was-hidden')
                .show();

            this.all_category_list_is_shown = true;
        }
        return this;
    },
    /**
     * Отображает / скрывает меню категорий товаров.
     */
    toggleProductCategoryMenu: function () {
        if (this.product_category_menu.css('display') === 'none') {
            this.showProductCategoryMenu();
        }
        else {
            this.hideProductCategoryMenu();
        }
    },
    showProductCategoryMenu: function () {
        this.product_category_menu.show();
        // перерасчет высоты окна.
        this.setMenuHeight(this.product_category_menu.find('.ui-product-menu-item-active'));
    },
    hideProductCategoryMenu: function () {
        this.product_category_menu.hide();
    },
    /**
     * Отображает / скрывает меню сфер деятельности.
     */
    toggleActivityMenu: function () {
        if (this.activity_menu.css('display') === 'none') {
            this.showActivityMenu();
        }
        else {
            this.hideActivityMenu();
        }
    },
    /**
     * Отображает меню сфер деятельности.
     */
    showActivityMenu: function () {
        this.activity_menu.show();
    },
    /**
     * Скрывает меню сфер деятельности.
     */
    hideActivityMenu: function () {
        this.activity_menu.hide();
    },

    /**
     * Задание минимальной высоты у главного меню сфер деятельности равной высоте подменю активного пункта.
     * Костыль из-за невозможности корректно сверстать выпадающие подменю с абсолютным позиционированием.
     */
    setMenuHeight: function ($element) {
        let $product_menu = $element.parents("ul.ui-product-menu"),
            $active_item = $product_menu.find("li.ui-product-menu-item-active"),
            subMenuHeight = $active_item.find("li.submenu-is-open > ul").outerHeight() || $active_item.children("ul").outerHeight();

        $product_menu.css("min-height", subMenuHeight);
    },

    /**
     * Обновление категорий сфер деятельностей.
     * force loading or update product categories by firm activities.
     */
    updateProductCategories: function (activity_id) {
        this.handleLoadActivityCategories(activity_id);
    }
});