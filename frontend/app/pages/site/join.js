import '../common';

import '../../forms/site/join';

import '../../widgets/ui-input-mail';
import '../../widgets/ui-multiply';
import '../../widgets/ui-collapse';
import '../../widgets/ui-input-select';
import '../../widgets/ui-input-photo';
import '../../widgets/ui-input-number';
import '../../widgets/ui-input-city';
import '../../widgets/ui-input-product';
// import '../../widgets/ui-select';
import '../../widgets/ui-category-new';
import '../../widgets/ui-autocomplete-company';

$(function () {

    $('#input-city').inputCity();
    $('#companyID').inputNumber();

    /**
     * Поиск компании.
     * todo: пока не нужен.
     */

    /*
    $('input.search-company').autocompleteCompany({
        select: function (event, ui) {
            $.ajax({
                url: '/api/profile/get', type: 'POST', data: {
                    id: ui.item.value,
                    columns: 'bin,title'
                }
            })
            .done(function (response) {
                let company_title = response.data.title,
                    company_bin = response.data.bin;

                // не нашел свою компанию.
                if (company_title === undefined && company_bin === undefined) {
                    $('input[name=company_title]').val('');
                    $('input[name=company_bin]').val('');
                }
                else {
                    $('input[name=company_title]').val(company_title);
                    $('input[name=company_bin]').val(company_bin);
                }
                $('.bin-control').show();
                $('.company-search-control-wrap')
                    .removeClass('company-search-control-wrap')
                    .addClass('form-control-group company-title-bin-group');

                // больше пользователь искать не может...
                $('input.search-company').autocompleteCompany('destroy');
            });
        },
        skip: function (event, ui) {
            $(this).trigger('nextStep');
        }
    });
    */

    $('.ui-category.activity-control')
        .category();

    $('.input-photo').each(function () {
        $(this).inputPhoto();
    });

    $('.js-collapse').collapse();

    $('#multiply-phone').multiply({
        max : 3,
        init: function (event, node) {
            let $inputs = $(node).find('input[type="tel"]');
            $inputs.mask('+7 (000) 000 00 00');
        }
    }).on("add", function (event) {
        let $input = $(event.target).find('input[type="tel"]');
        $input.mask('+7 (000) 000 00 00');
    });

    $('#multiply-email').multiply({
        max: 3
    });
});