/**
 * Работает с каталогом.
 */
class CategoryIndex {

    constructor() {
        this.body = $('body');
        this.relation_vocabulary_category = $('div#modal-add-category-vocabulary');
        this.catalog_state_cookie_key = 'Catalog_Opened_State';
    }

    handleVocabularyRelationFormSaved() {

        // форма для использования в списке характеристик категории(табличный вид.)
        if (this.relation_vocabulary_category.find('form#vocabulary-relation-form').hasClass('vocabulary-table-form-mode')) {
            location.reload();
            this.relation_vocabulary_category.modal('hide');
            return this;
        }

        if (this.relation_vocabulary_category.find('form#vocabulary-relation-form').hasClass('update-form')) {
            this.relation_vocabulary_category.modal('hide');
            return this;
        }

        // добавляю характеристику.
        let vocabulary_select = this.relation_vocabulary_category.find('select[name="vocabulary_id"]'),
            vocabulary_title = vocabulary_select.find('option:selected').text().trim(),
            vocabulary_id = vocabulary_select.find('option:selected').val(),
            category_id = this.relation_vocabulary_category.find('input[name=category_id]').val();

        let html = '<span>' +
                        '<a href="#" data-vocabulary-id="'+ vocabulary_id +'" data-category-id="'+category_id+'" class="categories-list__specification-item update-vocabulary">'+ vocabulary_title +'</a>' +
                        '<a href="#" data-vocabulary-id="'+ vocabulary_id +'" data-category-id="'+category_id+'" class="remove-vocabulary" title="Отвязать характеристику">' +
                            ' <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                        '</a> , ' +
                    '</span>';

        $(html).insertBefore(
            $('li.categories-list__category-item[data-category-id='+category_id+'] a.add-category-vocabulary:first')
        );

        this.relation_vocabulary_category.modal('hide');
        return this;
    }

    /**
     *
     * @param $a
     * @param for_tree - для дерева категорий?.
     */
    removeCategoryVocabulary($a, for_tree = true) {
        if (confirm('Отвязать характеристику от категории ?')) {
            let category_id = parseInt($a.data('category-id')),
                vocabulary_id = parseInt($a.data('vocabulary-id'))
            ;
            $.ajax({
                url: '/category/remove-vocabulary-category',
                type: 'POST',
                data: {category_id: category_id, vocabulary_id: vocabulary_id}
            })
            .success(function (response) {
                if (response.hasOwnProperty('success') && response.success) {
                    // для древа категорий.
                    if (for_tree) {
                        $a.parent().remove();
                    }
                    // для таблицы.
                    else {
                        $a.parents('tr:first').remove();
                    }
                }
            });
        }
    }

    loadChildCategories($element) {
        let category_id = parseInt($($element).data('category-id'))
            ,$li_category = $element.parent()
            ,need_load_child = $li_category.find('ul.categories-list:first').length === 0
        ;
        if (!$li_category.hasClass('categories-list__category-item_opened') && need_load_child) {
            $.ajax({url: '/category/load-child-categories', type: 'POST', data: {parent_id: category_id}})
                .success(function (response) {
                    $(response).insertAfter($li_category.find('div.categories-list__specifications-list'));
                    $li_category.toggleClass("categories-list__category-item_opened");
                });
            return false;
        }
        $li_category.toggleClass("categories-list__category-item_opened");
    }

    showChildCategories($element) {
        let parent = $element.parent();
        this.saveOpenedCategoryState(parseInt(parent.data('category-id')));
        parent.toggleClass("categories-list__category-item_opened");
        return this;
    }

    /**
     * Восстанавливает состояние каталога.
     */
    upCatalogState() {
        let key = this.catalog_state_cookie_key,
            from_cookie = $.cookie(key) || [],
            cookie_data = $.isArray(from_cookie) ? [] : from_cookie.toString().split(",");
        cookie_data.forEach(function(category_id) {
            $('li.categories-list__category-item.categories-list__category-item_parent[data-category-id='+category_id+']')
                .addClass('categories-list__category-item_opened');
        });
    }

    /**
     * Сохраняет состояние каталога.
     * @param category_id
     * @return {CategoryIndex}
     */
    saveOpenedCategoryState(category_id) {
        let key = this.catalog_state_cookie_key,
            from_cookie = $.cookie(key) || [],
            cookie_data = $.isArray(from_cookie) ? [] : from_cookie.toString().split(",")
        ;
        category_id = category_id.toString();
        if ($.inArray(category_id, cookie_data) > -1) {
            cookie_data.splice(cookie_data.indexOf(category_id), 1);
        }
        else {
            cookie_data.push(category_id);
        }
        $.cookie(key, cookie_data, {expires: 7, path: '/',});
        return this;
    }

    /**
     * Отображение модального окна привязки/редактирования связи характеристики.
     * @param $element
     */
    showModalVocabularyCategory($element) {
        let category_id = parseInt($element.data('category-id')),
            vocabulary_id = parseInt($element.data('vocabulary-id')),
            // модальное окно для табличного режима отображения характеристик категории.
            // @see: http://dashboard.trade.dev/category/vocabulary?id=55
            for_table_mode = $element[0].hasAttribute('data-vocabulary-table-mode') && $element.data('vocabulary-table-mode')
                ? 1 : 0;
        this.relation_vocabulary_category.modal('show');

        $('#modal-add-category-vocabulary-content').html('');
        let data_params = {category_id: category_id, for_table_mode: for_table_mode};
        if (parseInt(vocabulary_id) > 0) {
            data_params['vocabulary_id'] = vocabulary_id;
        }
        $.ajax({url: '/category/vocabulary-relation', type: 'GET', data: data_params})
            .success(function (response) {
                let $modal_add_category_vocabulary_content = $('#modal-add-category-vocabulary-content');
                $modal_add_category_vocabulary_content.html(response);
                // инициализация SelectizeControl.
                // @see: SelectizeControl::init();
                let Selectize = new SelectizeControl();
                Selectize.init();
                // инициализация CopyControl.
                $('div.copy-control').each(function() {
                    let Copy = new CopyControl($(this)).init();
                });
                $modal_add_category_vocabulary_content
                    .find('select[name=vocabulary_id]').change();
            });
    }

    /**
     * Обновляет каталог (на случай если появились новые данные).
     * @param $button
     */
    updateCatalog($button) {
        $button.attr('disabled', 'disabled');
        let $catalog_wrapper = $('div.catalog-wrapper')
            ,th = this
        ;
        $catalog_wrapper.addClass('load');
        $.ajax({
            url: '/category/update-catalog',
            type: 'POST',
            data: {parent_id: parseInt($button.data('parent-id'))}
        })
        .success(function (response) {
            $catalog_wrapper.html(response);
            th.upCatalogState();
        })
        .done(function() {
            $button.removeAttr('disabled', 'disabled');
            $catalog_wrapper.removeClass('load');
        });
    }

    /**
     * Инициализация событий страницы.
     */
    init() {

        let th = this;

        // обработчик сохранения формы связи характеристики с категорией.
        this.body.on('form.saved', '#vocabulary-relation-form', function () {
            th.handleVocabularyRelationFormSaved();
        });

        // отвязка характеристики от категории.
        this.body.on('click', 'a.remove-vocabulary', function (event) {
            event.preventDefault();
            let for_tree = !$(this).hasClass('for-table');
            th.removeCategoryVocabulary($(this), for_tree);
            return false;
        });

        // подгрузка дочерних категорий. (AJAX режим.)
        this.body.on('click', '.categories-list__category-item_parent .categories-list__category-item-link.ajax', function (event) {
            event.preventDefault();
            th.loadChildCategories($(this));
            return false;
        });

        // отображение дочерних категорий. (не - AJAX)
        this.body.on('click', '.categories-list__category-item_parent .categories-list__category-item-link:not(.ajax)', function (event) {
            event.preventDefault();
            th.showChildCategories($(this));
            return false;
        });

        // модальное окно с формой для связи характеристик с категорией.
        this.body.on('click', 'a.add-category-vocabulary, a.update-vocabulary', function(e) {
            e.preventDefault();
            th.showModalVocabularyCategory($(this));
            return false;
        });

        // обновление каталога.
        this.body.on('click', 'a.update-catalog-button', function (e) {
            e.preventDefault();
            th.updateCatalog($(this));
            return false;
        });

        // подгрузка формы настроек характеристики. (диапазон / список значений.)
        this.body.on('change', '#modal-add-category-vocabulary select[name="vocabulary_id"]', function() {
            let $option = $(this).find('option:selected'),
                vocabulary_id = parseInt($option.val()),
                vocabulary_type = parseInt($option.data('vocabulary-type')),
                is_type_range = $option.data('is-type-range'),
                is_type_select = $option.data('is-type-select');

            if (vocabulary_id > 0) {
                // блок настроек диапазона.
                if (is_type_range) {
                    $('div.range-settings').show();
                }
                else {
                    $('div.range-settings').hide();
                }
                // блок настроек опций select'a.
                if (is_type_select) {
                    $('div.select-settings').show();
                }
                else {
                    $('div.select-settings').hide();
                }
            }
            else {
                $('div.range-settings').hide();
                $('div.select-settings').hide();
            }
        });

        // восстанавливает состояние каталога.
        this.upCatalogState();
    }
}

$(function() {
    new CategoryIndex().init();
});