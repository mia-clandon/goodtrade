import '../../common';

import '../../../widgets/ui-input-product';
import '../../../widgets/ui-category-new';
import '../../../widgets/ui-input-photo';
import '../../../widgets/ui-delivery-condition';
import '../../../widgets/ui-input-hash';
//import '../../../widgets/ui-select';

import SelectizeControl from '../../../widgets/controls/SelectizeControl';
import RangeControl from '../../../widgets/controls/RangeControl';

/**
 * Работа с формой добавления / редактирования товара в кабинете.
 */
class Product {

    /**
     * Загружает характеристики для товара по категории.
     * @param category_id
     */
    loadVocabularies(category_id) {
        let th = this
            //,product_id = th.getProductId()
        ;
        $.ajax({
            url: '/product/get-vocabulary-terms-form',
            type: 'POST',
            data: {
                category_id: category_id, //product_id: product_id
            },
            success: function(response) {
                if (response !== "") {
                    let legend = $('.vocabulary-wrapper').children('legend')[0].outerHTML;

                    $('.vocabulary-wrapper')
                        .show()
                        .html(legend + response);

                    th.initControlScripts();
                } else {
                    $('.vocabulary-wrapper').hide();
                }
            }
        });
    }

    /**
     * Инициализация скриптов контролов.
     * @return Product
     */
    initControlScripts() {
/*
        $('.range-wrapper').each(function() {
            new RangeControl().init($(this));
        });
*/
        new SelectizeControl().init();
        $(".range_value").range({ type: "value" });
        $(".range").not(".range_value", ".range_balance").range();
        $('.ui-checkbox').checkbox();
        return this;
    }

    init() {
        let th = this;

        // todo wtf ?
        $('#input-product').inputProduct();

        // загрузка характеристик при изменении категории.
        $('select[name=category]').on('change', function () {
            let category_id = parseInt($(this).find('option:selected').val());
            th.loadVocabularies(category_id);
        });

        $('.input-photo').inputPhoto();
        $('.multiply-tech-specs').inputHash();
        $('#delivery-condition').deliveryCondition();

        $('#input-select-unit').select({
            change: function (e, data) {
                $('#input-range')
                    .find('.input-unit-right')
                    .text(data.text);
            }
        });

        // событие на добавление товара.
        $('button.add-product').on('click', function () {
            $('.cabinet-product-add-form').submit();
        });
        $('.cabinet-product-add-form').on('form.saved', function () {
            location.href = '/cabinet/product';
        });

        // Автоматическое увеличение текстовой области по высоте
        $('textarea').each(function () {
            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
        }).on('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
}

/**
 * Страница добавления товара из кабинета.
 */
$(function() {
    new Product().init();
});