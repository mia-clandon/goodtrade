$(function() {

    /** подтверждение удаления. */
    $('body').on('click', 'a.delete', function (e) {
        if ($(this).data('confirm')) {
            if (confirm($(this).data('confirm'))) {
                window.location.href = $(this).attr('href');
            }
            e.preventDefault();
            return false;
        }
    });
});