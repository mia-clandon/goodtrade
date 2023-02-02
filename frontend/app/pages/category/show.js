//import '../common';
import '../b2b/common';
import '../b2b/info-slider';
import '../b2b/news-slider';
import '../b2b/buttons-slider';

$(function () {
    /**
     * Подгрузка контента продукта.
     * todo: сделать классом. ООП.
     * @param category_id - идентификатор категории.
     * @param offset
     * @param callback
     */
    function loadProductList(category_id, offset, callback) {
        $.ajax({
            url: '/category/get-product-list', type: 'POST', data: {
                category_id: category_id, offset: offset,
            }
        }).done(function (data) {
            callback(data);
        });
    }

    $('.more_product_button').click(function () {
        let offset = $(this).data('offset');
        let category = $(this).data('category');

        loadProductList(category, offset, function (data) {
            $(".products_content").removeAttr('style');
            $(".products_content").find('.elements-grid__cell').removeAttr('style');
            $(".products_content").append(data);
            $('.more_product_button').data('offset', $('.more_product_button').data('offset') + $('.more_product_button').data('limit'));

            if ($('.more_product_button').data('offset') >= $('.more_product_button').data('count')) {
                $('.more_product_area').hide();
            }
            $("img.lazy").lazyload({effect: "fadeIn"});
        });
    });

    /**
     * Подгрузка контента фирмы.
     * @param category_id - идентификатор категории.
     * @param offset
     * @param callback
     */
    function loadFirmList(category_id, offset, callback) {
        $.ajax({
            url: '/category/get-firm-list', type: 'POST', data: {
                category_id: category_id, offset: offset,
            }
        }).done(function (data) {
            callback(data);
        });
    }

    $('.more_firm_button').click(function () {
        let offset = $(this).data('offset');
        let category = $(this).data('category');

        loadFirmList(category, offset, function (data) {
            $(".firms_content").removeAttr('style');
            $(".firms_content").find('.elements-grid__cell').removeAttr('style');
            $(".firms_content").append(data);
            $('.more_firm_button').data('offset', $('.more_firm_button').data('offset') + $('.more_firm_button').data('limit'));

            if ($('.more_firm_button').data('offset') >= $('.more_firm_button').data('count')) {
                $('.more_firm_area').hide();
            }
            $("img.lazy").lazyload({effect: "fadeIn"});
        });
    });


    $('.list-more-link').click(function (e) {
        e.preventDefault();
        $(this).siblings('li').show();
        $(this).siblings('.list-less-link').show();
        $(this).hide();
    });

    $('.list-less-link').click(function (e) {
        e.preventDefault();
        $(this).siblings('li.hidden').hide();
        $(this).siblings('.list-more-link').show();
        $(this).hide();
    });
});