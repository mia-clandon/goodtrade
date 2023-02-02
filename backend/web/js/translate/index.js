/**
 * Работает со статичными переводами.
 */
class TranslateIndex {

    constructor() {
        this.body = $('body');
        this.modal_block = $('div#modal-add-hint-translate');
    }

    setModalContent(content = '') {
        $('#modal-add-hint-translate-content').html(content);
        return this;
    }

    loadForm(hint_id) {
        let th = this;
        th.modal_block.modal('show');
        this.setModalContent();

        $.ajax({url: '/translate/add-translate/?hint-id=' + hint_id, type: 'GET',})
            .success(function (response) {
                th.setModalContent(response);
            });
    }

    init() {
        let th = this;

        this.body.on('form.saved', 'form.add-translate-form', function () {
            $.notify("Переводы сохранены.", "success");
        });

        // открытие модального окна.
        this.body.on('click', 'a.add-translate', function (e) {
            e.preventDefault();
            th.loadForm($(this).data('hint-id'));
            return false;
        });

        this.body.on('click','.language-tabs a', function (e) {
            e.preventDefault();
            $(this).tab('show');
            let $parent = $(this).parents('div.languages');
            $parent.find('.tab-pane.active').removeClass('active');
            $parent.find('.tab-pane[id='+$(this).attr('href')+']').addClass('active');
            return false;
        });
    }
}

$(function() {
    new TranslateIndex().init();
});