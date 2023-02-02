$(function() {
    var $modal = $('div#modal-term-value');
    // вызываем модальное окно с формой для редактирования значения.
    $('span.term-item').on('click', function (e) {
        e.preventDefault();
        $.ajax({url: $(this).find('a').attr('href'), type: 'GET'}).success(function(response) {
            $modal.find('.modal-body').html(response);
            $modal.modal('show');
        });
        return false;
    });
    // успешное сохранение значения характеристики.
    $('body').on('form.saved', 'form#term-update-form', function () {
        var value = $modal.find('input[name="value"]').val(),
            action = $modal.find('form').attr('action');
        $('span.term-item a[href^="'+action+'"]').text(value);
        $modal.modal('hide');
        return false;
    });
});