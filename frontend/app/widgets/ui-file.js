/**
 * Загрузчик файлов.
 * TODO: множественная загрузка файлов.
 * @author Артём Широких kowapssupport@gmail.com
 * TODO: Доработать контрол (удаление загруженных файлов.)
 */
export default $.widget('ui.fileUploader', {
    options: {
        input: null,
        // параметры загрузчика.
        params: {},
        files: [],
        xhr: null,
    },
    _create: function () {
        let $input = this.options.input = this.element.find('input[type=file]')
            , self_object = this;
        this.options.params = {
            url: this.element.data('url'),
            acceptFileTypes: this.element.data('accept-file-types'),
            maxFileSize: this.element.data('max-file-size'),
            maxNumberOfFiles: this.element.data('max-number-of-files'),
        };
        $input.fileupload({
            url: this.options.params.url,
            datatype: 'json',
            //dropZone: self.element,
            acceptFileTypes: new RegExp('/(\.|\/)(' + this.options.params.acceptFileTypes + ')$/i'),
            //limitConcurrentUploads: limitConcurrentUploads,
            maxFileSize: this.options.params.maxFileSize,
            maxNumberOfFiles: this.options.params.maxNumberOfFiles,
            replaceFileInput: false,
            formData: {
                file_name: $input.attr('name'),
            },
            progressall: function (e, data) {
                let progress = parseInt(data.loaded / data.total * 100, 10);
                self_object._handleUpload(progress);
            },
            add: function (e, data) {
                self_object._handleFileAdd(e, data);
            },
            send: function (e, data) {
                self_object._handleSend(e, data);
            },
            start: function (e) {
                self_object._handleUploadStart(e);
            },
            fail: function (e, data) {
                self_object._handleFail(e, data);
            }
        });
        // события.
        this._on(this.element, {'click a': this._handleClick});
        this._on(this.element, {'click .progress-bar-stop': this._handleCancel});
        this._on($input, {'fileuploaddone': this._handleDone});
        this._on($input, {'fileuploadsubmit': this._handleSubmit});
    },
    _init: function () {
    },
    _handleClick: function () {
        let $input = this.options.input;
        $input.click();
    },
    _handleSend: function (e, data) {},
    /**
     * Добавление файла.
     * @param e
     * @param data
     * @return {ui.fileUploader}
     * @private
     */
    _handleFileAdd: function (e, data) {
        let files = [];
        data.files.forEach(function (file) {
            files.push(file.name);
        });
        this.element.find('.input-file-thumbnails-wrap .input-file-text span')
            .text(files.join(', ').substr(0, 30) + ' ...');
        this.options.xhr = data.submit();
        return this;
    },
    /**
     * Начало загрузки файла.
     * @private
     */
    _handleUploadStart: function () {
        this.showProgressState();
        this.element.find('.input-file-thumbnails-wrap span.progress-bar')
            .attr('data-progress', 0);
        return this;
    },
    /**
     * Файл загружен.
     * @private
     */
    _handleDone: function (e, response) {
        let self = this,
            $input = this.option('input'),
            file_attribute = $input.attr('name');
        // файл загружен, добавляю hidden input.
        if (response.result.success == true) {
            this.addFile(
                response.result['file_name'],
                response.result['file_path']
            );
        }
        else {
            // подгрузка ошибок в контрол (результат загрузки файла на сервер).
            response.result.errors.forEach(function (error) {
                self.element.parents('.form-control').find('.form-control-message').append(
                    '<span>' + error + '</span>'
                );
            });
        }
    },
    addFile: function (name, path) {
        let $input = this.option('input'),
            file_attribute = $input.attr('name');
        this.options.files.push(name);
        this.showSuccessState();
        this.element.append($('<input>', {
            type: 'hidden',
            name: file_attribute + '[]',
            value: path
        }));
    },
    _handleCancel: function () {

        /*

        let self_object = this;
        if (this.options.xhr !== null) {
            this.options.xhr.abort();
        }
        this.options.files.forEach(function (file) {
            self_object.element.find('input[value=' + file + ']').remove();
        });
        self_object.element.parents('.form-control').find('.form-control-message').html('');

        */

        this.options.input.fileupload('destroy');
        this._clearErrors();

        this._create();
        this.showUploaderState();
    },

    _clearErrors: function() {
        this.element.parents('.form-control').find('.form-control-message').html('');
        return this;
    },

    /**
     * Обработчик ошибок загрузки файлов.
     * @param e
     * @param data
     * @private
     */
    _handleFail: function (e, data) {
        $.notify("Произошла ошибка загрузки файла, попробуйте позже.", "error");
        this.showUploaderState();
    },
    /**
     * Обработка процесса загрузки файлов.
     * @param percent
     * @return {ui.fileUploader}
     * @private
     */
    _handleUpload: function (percent) {
        this.element.find('.input-file-thumbnails-wrap span.progress-bar')
            .attr('data-progress', percent);
        return this;
    },
    _handleSubmit: function () {},
    /**
     * Отображение состояния для загрузки.
     */
    showUploaderState: function () {
        this.element.find('.input-file-placeholder').css({
            'display': 'inline-block'
        });
        this.element.find('.input-file-thumbnails-wrap').css({
            'display': 'none'
        });
    },
    /**
     * Отображение состояния прогресса загрузки.
     */
    showProgressState: function () {
        this.element.find('.input-file-placeholder').hide();
        this.element.find('.input-file-thumbnails-wrap').css({
            'display': 'inline-block'
        });
        this.element.find('.input-file-thumbnails-wrap.success').hide();
    },
    showSuccessState: function () {
        this.element.find('.input-file-placeholder').hide();
        this.element.find('.input-file-thumbnails-wrap').hide();
        this.element.find('.input-file-thumbnails-wrap.success').css({
            'display': 'inline-block'
        });
    }
});