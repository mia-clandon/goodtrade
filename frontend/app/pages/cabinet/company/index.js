import '../../common';

import '../../../widgets/ui-layout';
import '../../../widgets/ui-dropdown-menu';
import '../../../widgets/ui-category-new';
import '../../../widgets/ui-input-city';
import '../../../widgets/ui-multiply';
import '../../../widgets/ui-input-phone';
import '../../../widgets/ui-collapse';
import '../../../widgets/ui-input-photo';

$(function () {

    $('.dropdown').dropdownMenu();
    $('.ui-category').category();
    $('#input-city').inputCity();
    $('.js-collapse').collapse();
    $('.input-photo').inputPhoto();

    //js/pages/register/main.js:33

    $('#multiply-phone').multiply({
        max : 3,
        init: function(event,node) {
            let $inputs = $(node).find('input[type="tel"]');
            $inputs.mask('+7 (000) 000 00 00');
        }/*,
        add : function(event, node) {
            var $input = $(node).find('input[type="tel"]');
            $input.mask('+7 (000) 000 00 00');
        }*/
    });

    $('#multiply-email').multiply({
        max : 3
    });

    $('button.save-form').on('click', function() {
        $('form input[type=submit]').click();
    });

    // форма настроек организации успешно сохранилась.
    $('.cabinet-company-form').on('form.saved', function () {
        $.notify("Настройки организации успешно сохранены !", "success");
    });

});