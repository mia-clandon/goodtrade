/**
 * Работа с окэд.
 */
class Oked {

    constructor() {
        this.body = $('body');
        this.modal_block = $('div#modal-oked-category-relation');
    }

    /**
     * Загрузка формы привязки ОКЭД к категории.
     */
    loadForm(category_id) {
        let th = this;
        th.modal_block.modal('show');
        $('#modal-oked-category-relation-content').html('');

        $.ajax({url: '/category/oked-relation', type: 'GET', data: {category_id: category_id}})
            .success(function (response) {
                $('#modal-oked-category-relation-content').html(response);
            });
    }

    /**
     * @return {Number}
     */
    getCategoryId() {
        return parseInt($('form#oked-relation-form').find('input[name=category_id]').val());
    }

    /**
     * Поиск ОКЭД'а.
     * @param name
     * @param from
     * @param to
     * @param callback
     */
    findOked(name, from, to, callback) {
        let category_id = this.getCategoryId();
        $.ajax({
            url: '/api/oked/get',
            data: {name: name, from: from, to: to, category_id: category_id},
            type: 'POST'
        })
        .success(function(response) {
            // запрос прошел без ошибок.
            if (response.error === 0) {
                callback(response);
            }
        })
        .error(function() {
            alert('Произошла ошибка при поиске ОКЭД.');
        });
    }

    /**
     * Привязывает ОКЭДы к категории.
     * @param oked_list
     * @param callback
     */
    relateOked(oked_list, callback) {
        let category_id = this.getCategoryId();
        $.ajax({
            data: {category_id: category_id, items: oked_list},
            type: 'POST',
            url: '/category/do-oked-relation'
        })
        .success(function(data) {
            if (data.message === 'success') {
                callback(data);
            }
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже.');
        });
    }

    /**
     * Отвязывает ОКЭД от категории.
     * @param oked
     * @param callback
     */
    removeRelateOked(oked, callback) {
        let category_id = this.getCategoryId();
        $.ajax({url: '/category/remove-oked', type: 'POST', data: {
            category_id: category_id,
            oked: oked
        }})
        .success(function(data) {
            callback(data);
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже !');
        });
    }

    init() {
        let th = this;

        // открытие модального окна.
        this.body.on('click', 'a.oked-relation-link', function (e) {
            e.preventDefault();
            th.loadForm(parseInt($(this).data('category-id')));
            return false;
        });

        // поиск окэдов.
        this.body.on('click', 'button[name=find_oked]', function (e) {
           e.preventDefault();
           let from = parseInt($('input[name=from_oked]').val()),
               to = parseInt($('input[name=to_oked]').val()),
               name = $('input[name=name]').val(),
               button = $(this);

           $(this).attr('disabled', 'disabled');

           th.findOked(name, from, to, function(loaded) {
               let template_html = $('.found-oked-list-wrapper .oked-template').html()
                   ,html = '';
               button.removeAttr('disabled');

               loaded.data.forEach(function(oked) {
                   $('.found-oked-list-wrapper').show();
                    let oked_html =
                    template_html.replace(new RegExp('::value', 'g'), oked.key)
                             .replace(new RegExp('::name', 'g'), oked.name);
                    html += '<tr>' + oked_html + '</tr>';
               });

               if (loaded.data.length > 0) {
                   $('.do_relate_oked').show();
               }

               $('.found-oked-list-wrapper tr:not(.oked-template)').remove();
               $(html).insertAfter($('.found-oked-list-wrapper .oked-template'));
           });
           return false;
        });

        // привязка выбранных окэдов к категории.
        this.body.on('click', 'input[type=checkbox][name^=oked]', function () {
            let $tr = $(this).parents('tr:first'),
                checkbox = $(this);
            $(this).attr('disabled', 'disabled');

            if ($(this).is(':checked')) {
                // привязываю окед к категории.
                th.relateOked([parseInt($(this).val())], function() {
                    checkbox.removeAttr('disabled');
                    $tr.detach().appendTo($('.related-oked-list table tbody'));
                });
            }
            else {
                // отвязка.
                th.removeRelateOked(parseInt($(this).val()),function() {
                    checkbox.removeAttr('disabled');
                    $tr.detach().appendTo($('.found-oked-list-wrapper table tbody'));
                });
            }
        });
    }
}

$(function () {
    new Oked().init();
});