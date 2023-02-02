import '../../common';

import '../../../widgets/ui-file';

$(function () {

    //$('.input-file-uploader').fileUploader();

    let cache_button_name = '';
    $('button.save-form').on('click', function(e) {
        e.preventDefault();
        cache_button_name = $(this).html();
        $(this)
            .html('Загрузка ...')
            .attr('disabled', 'disabled');
        $('form input[type=submit]').click();
        return false;
    });

    // форма настроек пользователя успешно сохранилась.
    $('.cabinet-product-add-price-form')
        .on('form.saved', function () {
            $.notify("Прайс лист успешно отправлен на проверку.", "success");
            window.location.reload();
        })
        .on('form.done', function() {
            $('button.save-form')
                .html(cache_button_name)
                .removeAttr('disabled');
        })
    ;

    // после выбора файла сразу отправляю форму.
    $('input[name="price_file"]').on('change', function() {
        if ($(this).val().length > 0) {
            $('.save-form').click();
        }
    });
});