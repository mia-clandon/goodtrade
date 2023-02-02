require( [

    "jquery",
    "jquery-ui",
    'widgets/ui-tab-switcher',
    'widgets/ui-tabs',
    'widgets/ui-call-me',
    'widgets/ui-good-select',
    'slick'

], function($) {

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
        $.ajax({url: '/product/get-firm-product-list', type: 'POST', data: {
            category: [category_id], page: page, firm_id: firm_id
        }})
        .done(function (data) {
            callback(data);
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
            loadProductList(category_id, firm_id, 1, function (data) {
                //todo: прелоадер.
                tab_content.html(data);
                $("img.lazy").lazyload({effect : "fadeIn"});
            });
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
            if (data.toString().length == 0) {
                // товаров больше нет - прячу кнопку.
                th.hide();
            }
            th.removeClass('disabled');
            active_tab_content.find('.load-product-container').append(data);
            $("img.lazy").lazyload({effect : "fadeIn"});
        });
    });

    //$('#call-me-select').goodSelect();
    $('#feedback').slick({
        accessibility: false,
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 2000,
        arrows: false,
        dots: true,
        dotsClass: 'feedback-dots',
        draggable: true,
        infinite: true
    });
});