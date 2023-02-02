import '../b2b/common';
import '../b2b/buttons-slider';

/**
 * Скрипт страницы поиска.
 */
class Search {
    constructor() {
        this.form = $('form.filter-form');
    }
    /**
     * Загрузка данных со следующей страницы.
     */
    loadNextProductPageData(callback) {
        let current_page = parseInt($('input[name=product_found_page]').val());
        let next_page = current_page + 1;
        $.ajax({data: {page: next_page}, type: 'GET'})
            .done((response) => {
                if (response.hasOwnProperty('response')) {
                    $('div.row.product-list-wrapper').append(response.response);
                }
                if (response.hasOwnProperty('need_show_load_button')) {
                    if (response['need_show_load_button'] === false) {
                        $('.product-load-more').parents('div.row:first').remove();
                    }
                }
                this.reloadMasonry();
                $('input[name=product_found_page]').val(next_page);
                callback();
            })
        ;
    }
    reloadMasonry() {
        $(".elements-grid:not(.elements-grid_no-masonry) > div:first-child").masonry({
            // options
            itemSelector: '.elements-grid__cell',
            columnWidth: '.elements-grid__cell'
        });
        return this;
    }
    bindEvents() {
        $('button.product-load-more').on('click', (e) => {
            e.preventDefault();
            $(e.target).attr('disabled', 'disabled');
            this.loadNextProductPageData(()=>{
                $(e.target).removeAttr('disabled');
            });
            return false;
        });
    }
}

$(function() {
    new Search().bindEvents();

    // popup с формой фильтра поиска
    $('[data-type="filter-form"]').popup({
        // элемент при нажатии на который - будет открываться popup.
        toggleSelector: '[data-action="filter-form-toggle"]',
        // элемент, внутри которого находится переключатель и будет появляеться модальное окно
        wrapperSelector: '[data-type="popup-wrapper"]',
        // элемент, при нажатии на который будет будет происходить событие отмены
        cancelSelector: '[data-action="filter-form-close"]'
    });
});