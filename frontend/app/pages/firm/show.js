import '../common';

import '../../widgets/ui-collapse';
import '../../widgets/ui-tab-switcher';
import '../../widgets/ui-tabs';
import '../../widgets/ui-call-me';
import '../../widgets/ui-good-select';

/**
 * todo: Сделать классом ООП.
 */

$(function () {
    // Раскрываемые блоки описания
    if ($('.js-collapse').height() > 220) {
        $('.js-collapse').collapse({
            startContentHeight : 220
        });
    }
    else {
        $('.js-collapse').addClass("is-open");
        $('.js-collapse').find(".js-collapse-toggle").hide();
    }

    // подгрузка товаров организации.
    var active_tab = 0;

    /**
     * Подгрузка контента.
     * @param category_id - идентификатор категории.
     * @param firm_id - идентификатор организации.
     * @param page - страница для пагинатора.
     * @param callback
     */
    function loadProductList(category_id, firm_id, page, callback) {
        var category_array = isNaN(category_id)?[]:[category_id];
        $.ajax({
            url: '/product/get-firm-product-list',
            type: 'POST',
            data: {
                category: category_array, page: page, firm_id: firm_id
            },
            success: function (data) {
                callback(data);
            }
        });
    }
    /**
     * Переключатель вкладок.
     */
    $('#category-tabs').tabSwitcher({
        onTabSwitch: function (e, data) {
            var tab = data.tab,
                tab_content = data.tab_content,
                category_id = parseInt(tab.data('category-id')),
                firm_id = parseInt($('input[name=firm_id]').val())
                ;
            active_tab = data.tab_id;

            if(tab_content.find('.load-product-container').length == 0) {
                loadProductList(category_id, firm_id, 1, function (data) {
                    //todo: прелоадер.
                    tab_content.html(data);
                    $("img.lazy").lazyload({effect: "fadeIn"});

                    // добавление в cookies (товары/ избранное).
                    $('.js-keeper')
                        .keeper()
                        .on('updateCompare', function (event, object) {
                            //TODO  сделать чтоб обновлялся счетчик
                        });
                });
            }
        }
    });
    /**
     * Подгрузка товаров.
     */
    $(document.body).on('click', '.show-more', function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }

        var tab = $('#category-tabs').find('li.active'),
            active_tab_content = $('#'+active_tab),
            category_id = parseInt(tab.data('category-id')),
            page_input = active_tab_content.find('input[name=page]'),
            page = parseInt(page_input.val()),
            firm_id = parseInt($('input[name=firm_id]').val()),
            th = $(this)
        ;

        page_input.val(page + 1);
        th.addClass('disabled');

        loadProductList(category_id, firm_id, page + 1, function(data) {
            if (page_input.val() * th.data('limit') >= th.data('total-count')) {
                // товаров больше нет - прячу кнопку.
                th.hide();
            }
            th.removeClass('disabled');
            active_tab_content.find('.load-product-container').append(data);
            $("img.lazy").lazyload({effect : "fadeIn"});
        });
    });

    //$('#call-me-select').goodSelect();
});