require([
        "jquery",
        "jquery-ui",
        'jquery.mask',
        'widgets/ui-input-mail',
        'widgets/ui-delivery-condition',
        'widgets/ui-pick-category',
        'widgets/ui-multiply',
        'widgets/ui-collapse',
        'widgets/ui-input-select',
        'widgets/ui-input-photo',
        'widgets/ui-input-number',
        'widgets/ui-input-city',
        'widgets/ui-input-hash',
        'widgets/ui-product-category',
        'widgets/ui-input-product',
        'widgets/ui-select',
        'widgets/ui-category-new',
        'widgets/ui-autocomplete-company',
        'widgets/ui-tags-product',
        'widgets/ui-product',
        'widgets/ui-form-steps'
    ],
    function ($) {
        $(function () {

            $('#register').formSteps();
            $('#ui-product').product();
            $('#input-city').inputCity();
            $('#companyID').inputNumber();

            var $company_name = $('#companyName');
            $('input[name=search_company_bin]').autocompleteCompany({
                select: function (event, ui) {
                    $.ajax({
                        url: '/api/profile/get', type: 'POST', data: {
                            id: ui.item.value,
                            columns: 'bin,title'
                        }
                    })
                    .done(function (response) {
                        $company_name.val(response.data.title);
                        $('#companyID').val(response.data.bin);
                        $('.company-search-control-wrap').hide();
                        var $title_input = $('.company-title-bin-group');
                        var $bin_input = $('.company-activity-wrap');
                        // не нашел свою компанию.
                        if (response.data.bin === undefined && response.data.title === undefined) {
                            $bin_input.find('input').removeAttr('disabled');
                        }
                        $title_input.show();
                        $bin_input.show();
                    });
                },
                skip: function (event, ui) {
                    var value = ui.item.label;
                    $('#companyID').val(value);
                    if ($.isNumeric(value)) {
                    } else {
                        $company_name.val(value);
                    }
                    $(this).trigger('nextStep');
                    $company_name.focus();
                }
            });

            //todo;
            $('#ui-category').category({
                addCategory: function (e, data) {
                    // $(this).trigger('nextStep');
                    // $('#product-category').productCategory('add', data);
                },
                removeCategory: function(e, data) {
                    //$('#product-category').productCategory('del', data.id);
                }
            });

            $('.input-photo').each(function () {
                $(this).inputPhoto();
            });

            $('#input-hash').inputHash();
            $('.js-collapse').collapse();

            $('#multiply-phone').multiply({
                init: function (event, node) {
                    var $inputs = $(node).find('input[type="tel"]');
                    $inputs.mask('+7 (000) 000 00 00');
                }
            }).on("add", function (event) {
                var $input = $(event.target).find('input[type="tel"]');
                $input.mask('+7 (000) 000 00 00');
            });

            $('#multiply-email').multiply();
            $('#multiply-tech-specs').multiply();

            $('#input-select-unit').select({
                change: function (e, data) {
                    $('#input-range')
                        .find('.input-unit-right')
                        .text(data.text);
                }
            });

            $('#delivery-condition').deliveryCondition();
            $('#product-category').productCategory();
            $('#input-product').inputProduct();

        })
    }
);