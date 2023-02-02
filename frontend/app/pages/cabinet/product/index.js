import '../../common';

$(function () {
    /**
     * Переключение вкладок "товары/прайс-листы".
     */
    $('div.tumbler a').on('click', function () {
        if (parseInt($(this).data('count')) === 0) {
            return false;
        }
        if ($(this).hasClass('tumbler-button-active')) {
            return false;
        }
        let tumbler_active_class = 'tumbler-button-active';

        //вкладки.
        $(this).parents('.tumbler')
            .find('.' + tumbler_active_class)
            .removeClass(tumbler_active_class);
        $(this).addClass(tumbler_active_class);

        // прайс-листам не нужны фильтра и сортировка.
        if ($(this).data('tab') === 'price-list') {
            $('.sort').hide();
            $('.search-filters').hide();
        }
        else {
            $('.sort').show();
            $('.search-filters').show();
        }

        //контент.
        let tab_wrapper_id = "tab-" + $(this).data('tab');
        $('div.tab-wrapper').hide();
        $('#'+ tab_wrapper_id).show();
    });
});