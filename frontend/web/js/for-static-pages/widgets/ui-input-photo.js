define(['jquery', 'jquery-ui', 'jquery.fileupload'], function ($) {
    return $.widget('ui.inputPhoto', {
        options: {
            input: null,
            thumbnails: null,
            inputHiddenWrap: null,
            isMultiple: false,
            maxNumberOfFiles: 1,
            counter: 0,
            sortStartIndex: null,
            sortStopIndex: null,

            classes: {
                thumbnailsWrap: 'input-photo__thumbnails-wrap',
                thumbnails: 'input-photo__thumbnails',
                placeholder: 'input-photo__placeholder',
                inputHiddenWrap: 'input-hidden-wrap',
                addButton: 'input-photo-btn',
                delButton: 'input-photo-del'
            }
        },
        _create: function () {
            var $input = this.options.input = this.element.find('input:file'),
                isMultiple = this.options.isMultiple = $input.is('[multiple]'),
                self = this,
                maxNumberOfFiles = this.options.maxNumberOfFiles = (isMultiple) ? 4 : 1,
                limitConcurrentUploads = (isMultiple) ? 4 : 1;
            //(!isMultiple) && $input.prop('name', $input.prop('name').replace('[]', ''));
            $input.fileupload({
                url: '/api/uploader/upload',
                datatype: 'json',
                dropZone: self.element,
                acceptFileTypes: /(\.|\/)(jpeg|png)$/i,
                limitConcurrentUploads: limitConcurrentUploads,
                maxFileSize: 20000000,
                maxNumberOfFiles: maxNumberOfFiles,
                replaceFileInput: false,
                formData: {
                    param_name: $input.attr('name').replace('[]', ''),
                    thumbnails: JSON.stringify({
                        width: 50,
                        height: 50,
                        type: 'CROP'
                    })
                }
            });
            this.options.thumbnailsWrap = this.element.find('.' + this.options.classes.thumbnailsWrap);
            this.options.thumbnails = this.element.find('.' + this.options.classes.thumbnails);
            this.options.placeholder = this.element.find('.' + this.options.classes.placeholder);
            this.options.inputHiddenWrap = this.element.find('.' + this.options.classes.inputHiddenWrap);
            this.options.addButton = this.element.find('.' + this.options.classes.addButton);
            this._on(this.element, {'click a': this._handleClick});
            this._on($input, {'fileuploaddone': this._handleDone});
            this._on($input, {'fileuploadsubmit': this._handleSubmit});

            // Возможность перетаскивать миниатюры фотографий
            this.options.thumbnails.sortable({
                axis : "x",
                revert : true,
                containment : this.options.thumbnailsWrap,
                scroll : false
            });
            this.options.thumbnails.disableSelection();

            this._on(this.options.thumbnails, {'sortstart': this._handleSortStart});
            this._on(this.options.thumbnails, {'sortstop': this._handleSortStop});
        },
        _init: function () {
            var $inputHiddenWrap = this.option('inputHiddenWrap'),
                $inputs = $inputHiddenWrap.find('input:hidden'),
                current_images = this.options.input.attr('value');
            this.option('counter', $inputs.length);
            if (!$inputs.length) {
                try {
                    var images = JSON.parse(current_images);
                    this.option('counter', images.length);
                }
                catch (e) {
                    //do nothing.
                }
            }
        },
        _handleSubmit: function () {
            var maxNumberOfFiles = this.option('maxNumberOfFiles'),
                counter = this.option('counter');
            if (counter >= maxNumberOfFiles) {
                return false;
            }
            this.option('counter', ++counter);
        },
        _handleDone: function (e, data) {
            var self = this,
                $input = this.option('input'),
                file_attribute = $input.attr('name').replace('[]', '');
            $.each(data.result[file_attribute], function (i, file) {
                self._addItem(file);
            });
        },
        _handleClick: function (e) {
            var $el = $(e.target),
                $input = this.option('input'),
                action = $el.data('action');
            if (action == 'add') {
                $input.click();
            }
            else if (action == 'del') {
                this._removeItem($el.parent('li').index());
            }
            e.preventDefault();
        },
        _setOption: function (key, val) {
            var $placeholder = this.option('placeholder'),
                $thumbnailsWrap = this.option('thumbnailsWrap'),
                $button = this.option('addButton'),
                maxNumberOfFiles = this.option('maxNumberOfFiles');
            if (key == 'counter') {
                if (val) {
                    $placeholder.hide();
                    $thumbnailsWrap.show();
                    if (val >= maxNumberOfFiles) {
                        $button.hide();
                    }
                    else {
                        $button.show();
                    }
                }
                else {
                    $placeholder.show();
                    $thumbnailsWrap.hide();
                }
            }
            return this._super(key, val);
        },
        _addItem: function (data) {
            var $inputHiddenWrap = this.option('inputHiddenWrap'),
                $thumbnails = this.option('thumbnails'),
                $input = this.option('input'),
                $hidden = '<input type="hidden" name="' + $input.attr('name').replace('[]', '') + '[]" value="' + data.url + '">',
                $thumbnail = '<li>' +
                    '<img src="' + data.thumbnail + '" alt="' + data.name + '">' +
                    '<a href="#" role="button" class="' + this.options.classes.delButton + '" data-action="del"><a/>' +
                    '</li>';

            $thumbnails.append($thumbnail);
            $inputHiddenWrap.append($hidden);

            /*
            // Возможность выбрать главное фото по клику на миниатюру
            var $thumbLi = $thumbnails.children("li");

            if ($thumbLi.length == 1) {
                $thumbLi.attr("data-main-photo", "true").addClass("input-photo-thumbnail-main");
            }
            $thumbLi.parent().children("li:last-child").on("click", function (e) {
                if ($(this).attr("data-main-photo") === undefined) {
                    $(this).parent().children("li").each(function (index, element) {
                        $(element).removeClass("input-photo-thumbnail-main");
                        $(element).removeAttr("data-main-photo");
                    });
                    $(this).addClass("input-photo-thumbnail-main");
                    $(this).attr("data-main-photo", "true");
                }
            });
            */
        },
        _removeItem: function (index) {
            var $thumbnails = this.option('thumbnails'),
                $hiddens = this.option('inputHiddenWrap'),
                $items = $thumbnails.find('li'),
                $li = $items.eq(index),
                type = $li.data('type'),
                counter = ~-$items.length;
            if (type == 'from-server') {
                var image_for_remove_name = this
                        .option('input')
                        .attr('name')
                    + '[for_remove][]'
                ;
                this.element.append($('<input>', {
                    type: 'hidden',
                    name: image_for_remove_name,
                    value: $li.find('img').data('image-original')
                }));
            }
            $li.remove();
            $hiddens.find('input:hidden').eq(index).remove();
            this.option('counter', counter);

            //$thumbnails.children("li:first-child").addClass("input-photo-thumbnail-main").attr("data-main-photo", "true");
        },
        _handleSortStart: function (event, ui) {
            this.options.sortStartIndex = ui.item.index();
        },
        _handleSortStop : function (event, ui) {
            let startIndex = this.options.sortStartIndex,
                stopIndex = this.options.sortStopIndex = ui.item.index(),
                imgAlt = ui.item.children("img").attr("alt"), // В атрибуте alt хранится имя файла оригинала фотографии
                $inputHiddenWrap = this.options.inputHiddenWrap,
                $hiddenInput = $inputHiddenWrap.children('input[value$="' + imgAlt + '"]'); // В атрибуте value хранится полный путь до файла оригинала фотографии, с именем в конце

            if (startIndex === stopIndex) {
                return false;
            }
            //Перемещение скрытых полей в том же порядке, что и миниатюры.
            if (startIndex < stopIndex) {
                $inputHiddenWrap.children("input").eq(stopIndex).after($hiddenInput);
            } else {
                $inputHiddenWrap.children("input").eq(stopIndex).before($hiddenInput);
            }
        }
    });
});