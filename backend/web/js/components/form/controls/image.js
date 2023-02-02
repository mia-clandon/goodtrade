/**
 * Скрипт для работы с компонентом загрузки фото.
 * @author Артём Широких kowapssupport@gmail.com
 */
$(function() {

    //Если есть класс image-sorting то привязывает сортировку по перетаскиванию
    $('.image-sorting').sortable();

    $('[class^=component-upload-]').each(function() {

        var options = $(this).find('input[name=upload-component]');
        options = JSON.parse(options.val());

        //noinspection JSUnresolvedVariable
        var component_name = options.component_name
            ,is_multiple = options.is_multiple
            ,tmp_sizes = options.tmp_sizes
        ;
        var self = this;
        var $progress_wrapper = $(this).find('.progress-bar-wrap');
        var $form = $(this).parents('form');

        var file_upload_data = {
            param_name: component_name
        };
        if (!$.isEmptyObject(tmp_sizes)) {
            file_upload_data['thumbnails'] = JSON.stringify(tmp_sizes);
        }

        /**
         * Удаление фото.
         */
        $('a.thumbnail.preview-server span.glyphicon-remove').on('click', function() {
            var $th = $(this);
            if (confirm('Вы действительно хотите удалить фото ?')) {
                var image_id = parseInt($(this).data('image-id'))
                    ,action = $(this).data('remove-action')
                    ,entity_id = parseInt($(this).data('entity-id'))
                ;
                $.ajax({
                    data: {image_id: image_id, entity_id: entity_id},
                    type: 'POST',
                    url: action
                })
                .success(function() {
                    $th.parents('div.image-block').remove();
                })
                .error(function() {
                    alert('Произошла ошибка, попробуйте позже.');
                });
            }
        });

        /**
         * Загрузчик.
         */
        $('input[name="'+component_name+'"]').fileupload({
            dataType: 'json',
            formData: file_upload_data,
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $progress_wrapper.find('.pull-right.text-muted').text(progress + '% Загружено');
                $progress_wrapper.find('.progress-bar').css(
                    'width',
                    progress + '%'
                );
                $progress_wrapper.find('.progress-bar span.sr-only').text(progress + '% Загружено');
                $progress_wrapper.show();
            },
            done: function (e, data) {

                var result = data.result[component_name];

                result.forEach(function(img) {
                    $form.append($('<input />', {
                        //name: (is_multiple) ? component_name+'[]' : component_name,
                        name: component_name + '[]',
                        value: img.url,
                        type: 'hidden'
                    }));
                    var thumbnail = $('<div>', {class: 'col-xs-6 col-md-3'}).append(
                        $('<a>', {class: 'thumbnail', href: '#'}).append(
                            $('<img>', {src: img.thumbnail})
                        )
                    );
                    if (is_multiple) {
                        $progress_wrapper.find('div.previews').append(thumbnail);
                    }
                    else {
                        $progress_wrapper.find('div.previews').html(thumbnail);
                    }
                });
                // progress bar hide
                //$progress_wrapper.find('.progress').hide();
            }
        });
    });
});