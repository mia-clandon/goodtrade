/**
 * Обработчик формы регистрации.
 * @author Артём Широких kowapssupport@gmail.com
 */
$(function() {
    var join_form = $('.join-user-form')
        ,first_step_attributes = [
            'company_title', 'company_image', 'company_bin', 'company_activity',
            'company_location', 'company_legal_address', 'company_phone', 'company_email',
            'company_bank', 'company_bik', 'company_iik', 'company_kbe', 'company_knp'
        ]
    ;
    // отправка формы.
    $('body').on('click', 'a[href="#finish"]', function() {
        join_form.submit();
    });
    join_form
        .on("form.saved", function() {
            // переношу пользователя на главную страницу.
            // todo - после разработки страницы авторизованного пользователя сделать редирект на неё;
            document.location.href = '/cabinet';
        })
        .on("form.errors", function(e, data) {
            var errors = data.errors
                ,has_first_step_errors = false
            ;
            $.each(errors, function(index) {
                if (first_step_attributes.indexOf(index)) {
                    has_first_step_errors = true;
                }
            });
            if (has_first_step_errors) {
                // переношу пользователя на первый шаг.
                $('a[href="#previous"]').click();
            }
        })
    ;
});