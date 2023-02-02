var PartIndexer = {
    options: {
        form: null,
        indexer_url: null,
        clear_index_url: null,
    },
    sendPart: function($part) {

        var offset = parseInt($part.data('offset'))
            ,limit = parseInt($part.data('limit'))
            ,$button = $part.find('button.start-part')
            ,$calculate_button = $('#calculate-indexer-parts')
            ,self = this
        ;

        $part.find('button.start-part').attr('disabled', 'disabled');
        $calculate_button.attr('disabled', 'disabled');

        $.ajax({
            url: self.options.indexer_url,
            data: {offset: offset, limit: limit},
            type: 'POST',
            dataType: 'json'
        })
        .success(function(data) {
            if (data.success) {
                $button.attr('processed', 'processed');
                $button.addClass('btn-success');
                $button.text('OK');
                self.getNextPart($part);
            }
            $button.removeAttr('disabled');
            $calculate_button.removeAttr('disabled');
        })
        .error(function() {
            $button.removeAttr('disabled');
            $calculate_button.removeAttr('disabled');
            $button.addClass('btn-danger');
            alert('Произошла ошибка, попробуйте позже.');
        });
    },
    /**
     * Получение следующий порции.
     * @param $current_part
     */
    getNextPart: function($current_part) {
        $current_part.next().find('button.start-part').click();
    },
    /**
     * Расчет количества порций.
     */
    calculate: function() {
        var part_count = parseInt(this.options.form.find('[name=part_count]').val())
            ,action = this.options.form.attr('action')
            ;
        $.ajax({
            url: action,
            data: {part_count: part_count},
            type: 'POST'
        })
        .success(function(m) {
            $('div.parts-table').html(m);
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже.');
        });
    },
    /**
     * Очистка индекса.
     * @param button
     */
    clearIndex: function(button) {
        var self_object = this;
        if (button.attr('disabled') != 'disabled') {
            button.attr('disabled', 'disabled');
            $.ajax({url: self_object.options.clear_index_url})
            .error(function() {
                button.removeAttr('disabled');
            })
            .done(function() {
                button.removeAttr('disabled');
            });
        }
    },
    bindEvents: function() {
        var self_object = this;
        // очистка индекса.
        $('#clear-index').click(function(e) {
            e.preventDefault();
            self_object.clearIndex($(this));
        });
        // модальное окно.
        $('#update-index').click(function (e) {
            e.preventDefault();
            if (confirm('В случае не понимания функционала данной кнопки - закройте окно !')) {
                $.fancybox({
                    padding: 0,
                    width: 700,
                    height: 610,
                    autoSize: false,
                    content: $('#update-index-modal')
                });
            }
            return false;
        });
        // расчёт порций для индексации.
        $('#calculate-indexer-parts').click(function () {
            self_object.calculate();
        });
        // запуск порции на индексацию.
        $('body').on('click', 'button.start-part', function () {
            self_object.sendPart($(this).parents('tr'));
        });
    },
    init: function(options) {
        this.options = $.extend(this.options, options);
        this.bindEvents();
        return this;
    }
};