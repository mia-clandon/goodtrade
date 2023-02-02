$(function () {
    $('body').on('click', 'button[type=submit]', function () {
        $(this).parents('form').submit();
    });
    $('.login-form').on("form.saved", function () {
        document.location.href = '/';
    });
});