import '../common';

$(function () {

    /**
     * Подгрузка контента продукта.
     * @param firm_id
     * @param callback
     */
    function loadFavoriteProductList(firm_id, callback) {
        $.ajax({url: '/favorite/get-product-list', type: 'POST', data: {
            firm_id: firm_id,
        }})
            .done(function (data) {
                callback(data);
            });
    }

    /**
     * Подгрузка контента хронологии.
     * @param firm_id
     * @param callback
     */
    function loadFavoriteChronoList(firm_id, callback) {
        $.ajax({url: '/favorite/get-chronos-list', type: 'POST', data: {
            firm_id: firm_id,
        }})
            .done(function (data) {
                callback(data);
            });
    }

    $('.js-show-products').click(function () {
        let firm_id = $('li.js-show-products').data('firm-id');
        $('li.js-show-products').siblings('li').removeClass('is-active');
        $('li.js-show-products').addClass('is-active');

        loadFavoriteProductList(firm_id, function (data) {
            $('.js-favorite-content').html(data);
        });

    });

    $('.js-show-chronos').click(function () {
        let firm_id = $('li.js-show-chronos').data('firm-id');
        $('li.js-show-chronos').siblings('li').removeClass('is-active');
        $('li.js-show-chronos').addClass('is-active');

        loadFavoriteChronoList(firm_id, function (data) {
            $('.js-favorite-content').html(data);
        });
    });

    $('.js-favorite-note').change(function () {
        let note = $('.js-favorite-note').val();
        let firm_id = $('.js-favorite-note').data('firm-id');
        $.ajax({url: '/favorite/set-user-note', type: 'POST', data: {
            note: note,
            firm_id: firm_id,
        }})
    });

    $('.js-keeper')
        .keeper()
        .on('updateCompare', function () {
            window.location.href = '/favorite';
        });
});