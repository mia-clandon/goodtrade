$(function() {
    $('select[name=type]').on('change', function () {
        if ($(this).find('option:selected').val() === '2') {
            $('.range-settings').show();
        }
        else {
            $('.range-settings').hide();
        }
    })
});