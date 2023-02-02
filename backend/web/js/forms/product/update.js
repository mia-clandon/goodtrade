/**
 * Работа с формой редактирования/добавления товаров.
 */
class Product {

    /**
     * Загружает характеристики для товара по категории.
     * @param category_id
     */
    loadVocabularies(category_id) {
        let th = this
            ,product_id = th.getProductId()
        ;
        $.ajax({
            url: '/product/get-vocabulary-terms-form',
            type: 'POST',
            data: {
            category_id: category_id, product_id: product_id
            },
            success: function(response) {
                $('.vocabulary-wrapper').html(response);
                th.initControlScripts();
            }
        });
    }

    /**
     * Загружает города для товара по региону.
     * @param region_id
     */
    loadCities(region_id) {
        $.ajax({
            url: '/location/get-locations-by-region',
            type: 'POST',
            data: {
                region_id: region_id
            },
            success: function(response) {
                $('select.place_city_select').find('option').remove();
                $('select.place_city_select').append('<option value="">Не выбран</option>');
                $.each(response, function (index, value) {
                    $('select.place_city_select').append('<option value="'+value.id+'">'+value.title+'</option>');
                });
            }
        });
    }

    /**
     * Загружает масто реализации.
     */
    loadPlaces() {
        let th = this
            ,product_id = th.getProductId()
        ;
        $.ajax({
            url: '/product/get-places-form',
            type: 'POST',
            data: {
            product_id: product_id
            },
            success: function(response) {
                $('.places-wrapper').html(response);
                th.initControlScripts();
            }
        });
    }

    setProductId(product_id) {
        this.product_id = product_id;
        return this;
    }

    getProductId() {
        return this.product_id;
    }

    /**
     * Инициализация скриптов контролов.
     * @return Product
     */
    initControlScripts() {
        $('.range-wrapper').each(function() {
            new RangeControl().init($(this));
        });
        new SelectizeControl().init();
        return this;
    }

    init() {
        let $body = $('body')
            ,th = this;

        $body.on('change', 'select[name=category_id]', function () {
            this.category_id = parseInt($(this).val());
            th.loadVocabularies(this.category_id);
        });

        $body.on('change', 'select.place_region_select', function () {
            this.region_id = parseInt($(this).val());
            th.loadCities(this.region_id);
        });
    }
}

$(function() {
    let product = new Product(),
        product_id = parseInt($('input[name=product_id]').val()),
        category_id = parseInt($('select[name=category_id] option:selected').val());
    product.setProductId(product_id);
    // для существующего товара - подгружаю характеристики.
    if (product_id !== 0) {
        product.loadVocabularies(category_id);
        product.loadPlaces();
    }
    product.init();
});