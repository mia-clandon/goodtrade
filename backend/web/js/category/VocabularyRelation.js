/**
 * Работает со списком связанных и унаследованных характеристик категории.
 */
class VocabularyRelation {

    constructor() {
        this.body = $('body');
    }

    updateVocabularyPosition() {
        let data = [];
        $('table.table-category-vocabulary').find('tbody tr').each(function() {
            data.push({
                category_id: parseInt($(this).data('current-category-id')),
                vocabulary_id: parseInt($(this).data('vocabulary-id')),
            });
        });
        $.ajax({url: '/category/update-category-vocabulary-positions', type: 'POST', data: {positions: data}})
            .success(function(response) {
                if (response.hasOwnProperty('success') && response.success) {
                    $.notify("Позиции обновлены.", "success");
                }
                else {
                    $.notify(response.error, "error");
                }
            })
            .error(function (response) {
                console.log(response.statusText);
                $.notify("Произошла ошибка, попробуйте позже !", "error");
            });
    }

    init() {
        let th = this;

        // поднятие строки характеристики на позицию выше.
        this.body.on('click', 'a.up-vocabulary-position', function(e) {
            e.preventDefault();
            let row = $(this).parents('tr:first');
            row.insertBefore(row.prev());
            th.updateVocabularyPosition();
        });

        // поднятие строки характеристики на позицию ниже.
        this.body.on('click', 'a.down-vocabulary-position', function(e) {
            e.preventDefault();
            let row = $(this).parents('tr:first');
            row.insertAfter(row.next());
            th.updateVocabularyPosition();
        });

        this.body.on('click', 'input.update-property', function() {
            // todo: вынести в метод.
            let category_id = $(this).data('category-id'),
                vocabulary_id = $(this).data('vocabulary-id'),
                property = $(this).data('property-name'),
                flag = $(this).is(':checked'),
                post_object = {
                    category_id: category_id,
                    vocabulary_id: vocabulary_id,
                    property: property,
                    flag: flag ? 1 : 0
                };
            $.ajax({url: '/category/update-category-vocabulary-property', type: 'POST', data: post_object})
                .success(function(response) {
                    if (response.hasOwnProperty('success') && response.success) {
                        $.notify("Данные обновлены.", "success");
                    }
                    else {
                        $.notify("Произошла ошибка, попробуйте позже !", "error");
                    }
                })
                .error(function (response) {
                    console.log(response.statusText);
                    $.notify("Произошла ошибка, попробуйте позже !", "error");
                });
        });
    }
}

$(function() {
    new VocabularyRelation().init();
});