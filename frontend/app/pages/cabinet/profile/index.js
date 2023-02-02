import '../../common';

import '../../../widgets/ui-input-photo';

$(function () {

    $('.input-photo').each(function(){
        $(this).inputPhoto();
    });

    $('button.save-form').on('click', function() {
        $('form input[type=submit]').click();
    });

    // форма настроек пользователя успешно сохранилась.
    $('.cabinet-profile-form').on('form.saved', function () {
        $.notify("Профиль пользователя успешно сохранен !", "success");
    });
});