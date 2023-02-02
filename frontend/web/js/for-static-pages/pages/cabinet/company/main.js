require([
    'jquery',
    'jquery-ui',
    'jquery.mask',
    'widgets/ui-layout',
    'widgets/ui-dropdown-menu',
    'widgets/ui-category-new',
    'widgets/ui-input-city',
    'widgets/ui-multiply',
    'widgets/ui-input-phone',
    'widgets/ui-collapse',
    'widgets/ui-input-photo',
    "notify",

], function ($) {

    $('.dropdown').dropdownMenu();
    $('#ui-category').category();
    $('#input-city').inputCity();
    $('.js-collapse').collapse();
    $('.input-photo').inputPhoto();

    //js/pages/register/main.js:33

    $('#multiply-phone').multiply({
        init: function(event,node) {
            var $inputs = $(node).find('input[type="tel"]');
            $inputs.mask('+7 (000) 000 00 00');
        },
        add : function(event, node) {
            var $input = $(node).find('input[type="tel"]');
            $input.mask('+7 (000) 000 00 00');
        }
    });

    $('#multiply-email').multiply();

    $('button.save-form').on('click', function() {
        $('form input[type=submit]').click();
    });

    // форма настроек организации успешно сохранилась.
    $('.cabinet-company-form').on('form.saved', function () {
        $.notify("Настройки организации успешно сохранены !", "success");
    });
});