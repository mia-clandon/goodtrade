require([
    'jquery',
    'jquery-ui',
    'widgets/ui-file',
    'notify',
], function ($) {

    $('.input-file-uploader').fileUploader();

    $('button.save-form').on('click', function() {
        $('form input[type=submit]').click();
    });

    // форма настроек пользователя успешно сохранилась.
    $('.cabinet-product-add-price-form')
        .on('form.saved', function () {
            $.notify("Прайс лист успешно отправлен на проверку.", "success");
        })
        .on('form.errors', function (e, errors) {
            for(var property in errors.errors) {
                if (errors.errors.hasOwnProperty(property)) {
                    errors.errors[property].forEach(function (error) {
                        $.notify(error, "error");
                    });
                }
            }
        })
    ;
});