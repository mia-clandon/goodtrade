define(['jquery','jquery-ui','jquery.fileupload'],function($) {
    return $.widget('ui.logo', {
        options: {
            thumbnails: null,
            thumbnailsWrap: null,
            inputField: null,
            placeHolder: null
        },
        _create: function () {
            this.options.placeholder = $('#input-logo-placeholder');
            this.options.dropzone = $('#input-logo-dropzone');
            this.options.thumbnails = $('#input-logo-thumbnails');
            this.options.thumbnailsWrap = this.options.thumbnails.closest('.input-photo-thumbnails-wrap');
            this.options.inputField = $('#input-logo');
            this._on(this.element, {'click': this._handleClick});
            this._refresh();
        },
        _refresh: function () {
            var dropZone = this.options.dropZone,
                placeholder = this.options.placeholder,
                thumbnailsWrap = this.options.thumbnailsWrap,
                thumbnails = this.options.thumbnails,
                html = '',
                self = this;
            this.options.inputField = this.element.find('input[type="file"]');
            this.options.inputField.fileupload({
                dropZone: dropZone,
                formData: {
                    thumbnails: JSON.stringify({
                        width: 150, // обязательна для ресайза
                        height: 150,
                        type: 'CROP' // возможные типы (CROP, NONE, WIDTH, HEIGHT, AUTO)
                    })
                },
                add: function (e, data) {
                    console.log('asd');
                    if (placeholder.hasClass('is-hidden')) {
                        placeholder.addClass('is-hidden');
                    }
                    if (!thumbnailsWrap.hasClass('is-visible')) {
                        thumbnailsWrap.addClass('is-visible');
                    }
                    html += '<li>';
                    html += '<img src="../public/img/preloader.gif">';
                    html += '<i class="icon icon-close" data-action="delete"></i>';
                    html += '</li>';
                    thumbnails.append(html);
                },
                done : function (e,data) {
                    console.log(data);
                }
            });
        },
        _handleClick: function (e) {
            var $el = $(e.target),
                input = this.options.inputField,
                thumbnails = this.options.thumbnails,
                thumbnailsWrap = this.options.thumbnailsWrap,
                placeholder = this.options.placeHolder,
                clone = null;
            switch ($el.data('action')) {
                case 'open' :
                    input.click();
                    break;
                case 'delete' :
                    clone = this.options.inputField.clone(true);
                    this.options.inputField.replaceWith(clone);
                    placeholder.removeClass('is-hidden');
                    thumbnailsWrap.removeClass('is-visible');
                    thumbnails.html('');
                    this._refresh();
                    break;
            }
        },
        _handleAdd: function (e, data) {
            if (!$placeholder.hasClass('is-hidden')) {
                $placeholder.addClass('is-hidden');
            }
            if (!$thumbnailsWrap.hasClass('is-visible')) {
                $thumbnailsWrap.addClass('is-visible');
            }
            html += '<li>';
            html += '<img src="../public/img/preloader.gif">';
            html += '<i class="icon icon-close" data-action="delete"></i>';
            html += '</li>';
            $thumbnails.append(html);
        }

    });
});