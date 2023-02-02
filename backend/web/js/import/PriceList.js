/**
 * Объект работает с листом товарных позиций прайс листа.
 * TODO: вынести в настройки все возможные селекторы.
 */
var PriceList = {
    options: {
        form: null,
        table_selector: null
    },
    // table cache.
    table_cache: null,
    // данные товаров с прайс листа.
    data: {},
    // observable: data is loaded ?
    ko_data_loaded: ko.observable(false),
    // observable vocabularies.
    ko_vocabularies: ko.observableArray([]),
    // observable images.
    ko_images: ko.observableArray([]),
    // текущий id.
    current_id: 0,
    // загрузчик.
    uploader: null,
    /**
     * Сериализация данных формы.
     * @param form
     * @returns {{}}
     */
    serializeForm: function(form) {
        var data = {};
        form.serializeArray().map(function (x) {
            data[x.name] = x.value;
        });
        return data;
    },
    /**
     * Метод загружает данные по прайс листу с сервера.
     * Пополняет модель данными.
     */
    loadList: function() {
        var form = this.options.form
            ,self_object = this;

        form.validate(function(is_valid) {
            if (is_valid) {
                var submit = form.find('[type=submit]')
                    ,url = submit.data('url')
                    ,id = parseInt(form.find('[name=id]').val())
                    ,data = self_object.serializeForm(form)
                ;
                submit.attr('disabled', 'disabled');
                $.ajax({data: data, url: url, type: 'POST'})
                    .success(function (data) {
                        self_object.afterLoadData(data);
                        submit.removeAttr('disabled');
                    })
                    .error(function () {
                        alert('Произошла ошибка, попробуйте позже.');
                        submit.removeAttr('disabled');
                    })
                ;
            }
        });
    },
    afterLoadData: function(data) {
        $('#excel-table').html(data);
        this.loadProductData();
        // подсветка строк с одинаковыми названиями товаров.
        this.highLightSameRows();
    },
    /**
     * Загрузка данных в data.
     */
    loadProductData: function () {
        var self_object = this;
        this.clearProductData();

        // загрузка данных с таблицы в объект.
        this.getTable().find('tbody tr.product-row').each(function() {
            var product_id = parseInt($(this).data('id'))
                ,title = $(this).find('td.title-col input').val().trim()
                ,price = $(this).find('td.price-col input').val().trim()
            ;
            self_object.data[product_id] = {
                product_id: product_id,
                title: title,
                price: price,
                vocabularies: [],
                images: [],
                category: null,
            }
        });

        this.ko_data_loaded(true);
    },
    getPriceData: function() {
        return this.data;
    },
    getProductData: function(id) {
        return this.data[id];
    },
    getTable: function() {
        if (this.table_cache === null) {
            this.table_cache = $(this.options.table_selector);
        }
        return this.table_cache;
    },
    getTableRow: function (product_id) {
        return this.getTable().find('.product-row[data-id='+product_id+']');
    },
    getVocabularies: function() {
        return this.ko_vocabularies;
    },
    /**
     * Подсветка одинаковых товаров. (по названию)
     */
    highLightSameRows: function () {
        var self_object = this;
        self_object.getTable().find('tbody tr td.title-col input').each(function () {
            var title = $(this).val().trim();
            var same_title = self_object.getTable().find('tbody tr td.title-col input[value*="'+title+'"]');
            if (same_title.length > 1) {
                same_title.parents('tr').addClass('danger');
            }
        });
    },
    /**
     * Метод включает выбор колонок характеристик.
     */
    enableSelectVocabularyColumns: function () {
        var vocabulary_columns = this.getTable().find('thead td:not(".title-col"):not(".price-col"):not(".actions-col")');
        vocabulary_columns.each(function() {
            if ($(this).html().length) {
                var checkbox = $('<div />', {class:'checkbox'})
                        .append(
                            $('<label />')
                                .append($('<input>', {type: 'checkbox', name: 'vocabulary-column[]'}))
                                .append('Характеристика')
                        )
                    ;
                $(this).append(checkbox);
            }
        });
    },
    /**
     * Метод выключает выбор колонок характеристик.
     */
    disableSelectVocabularyColumns: function() {
        var vocabulary_columns = this.getTable().find('thead td:not(".title-col"):not(".price-col"):not(".actions-col")');
        vocabulary_columns.each(function() {
            var checkbox = $(this).find('.checkbox');
            if (checkbox.length) {
                checkbox.remove();
            }
        });
    },
    /**
     * Метод добавляет выбранные колонки (характеристики к товарам.)
     */
    appendVocabulariesToItems: function() {

        var vocabulary_checkboxes = $('input[name^=vocabulary-column]:checked')
            ,self_object = this
        ;

        // список характеристик (выбранные колонки).
        var vocabulary_data = {};
        vocabulary_checkboxes.each(function() {
            var checkbox = $(this)
                ,td = checkbox.parents('td')
                ,td_index = td.index()
            ;
            vocabulary_data[td_index] = td.find('.head-col-content').text().trim();
        });

        // заполняет характеристики каждому товару в объект this.data
        this.getTable().find('tbody tr.product-row').each(function() {
            var data = []
                ,product_id = parseInt($(this).data('id'))
            ;
            for (var index in vocabulary_data) {
                var vocabulary = new Vocabulary();
                    vocabulary.setTitle(vocabulary_data[index]);
                var term = new Term();
                    term.setTitle($(this).find('td').eq(index).text().trim());
                vocabulary.addTerm(term);
                data.push(vocabulary);
            }
            self_object.data[product_id]['vocabularies'] = data;
        });
    },
    /**
     * Очищает данные по товарам.
     * @returns {PriceList}
     */
    clearProductData: function() {
        this.data = [];
        return this;
    },
    removeRow: function (id) {
        if (this.data[id]) {
            delete this.data[id];
        }
        return this;
    },
    removeTableRow: function(id) {
        this.getTableRow(id).remove();
        this.removeRow(id);
        return this;
    },
    showProductModal: function() {
        $('#import-vocabulary-create').modal('show');
        return this;
    },
    showProductCategoryModal: function() {
        $('#product-category-relation').modal('show');
        return this;
    },
    closeProductCategoryModal: function() {
        $('#product-category-relation').modal('hide');
        return this;
    },
    getCurrentId() {
        return parseInt(this.current_id);
    },
    /**
     * @param {Vocabulary} vocabulary
     */
    addTerm: function(vocabulary) {
        var term = new Term();
        vocabulary.addTerm(term);
        this.ko_vocabularies.refresh();
    },
    addVocabulary: function() {
        var vocabulary = new Vocabulary();
            vocabulary.addTerm(new Term());
        this.ko_vocabularies.push(vocabulary);
        return this;
    },
    /**
     * Объединение характеристик товара (и удаление всех кроме 1го).
     */
    mergeVocabularies: function() {
        //TODO:
    },
    _bindEvents: function() {
        var self_object = this
            ,body = $('body')
        ;

        // загрузка прайс листа.
        this.options.form.on('submit', function(e) {
            e.preventDefault();
            self_object.loadList();
        });

        // отображение чекбоксов для выбора колонок с характеристиками.
        body.on('click', 'button.show-vocabulary-columns', function(e) {
            e.preventDefault();
            if (confirm('Внимание, данное действие перетрёт все существующие характеристики.')) {
                self_object.enableSelectVocabularyColumns();
                $('button.add-vocabulary-columns').removeAttr('disabled');
                $(this).attr('disabled', 'disabled');
            }
        });

        // добавление выбранных колонок характеристик к характеристикам товаров.
        body.on('click', 'button.add-vocabulary-columns', function(e) {
            e.preventDefault();
            self_object.appendVocabulariesToItems();
            $('button.show-vocabulary-columns').removeAttr('disabled');
            $(this).attr('disabled', 'disabled');
            // прячу чекбоксы колонок характеристик.
            self_object.disableSelectVocabularyColumns();
        });

        // удаление элемента прайс листа.
        body.on('click', 'a.remove-row', function(e) {
            e.preventDefault();
            if (confirm('Вы действительно хотите удалить позицию?')) {
                self_object.removeTableRow($(this).data('id'));
            }
        });

        // смена названия товара.
        body.on('change', 'td.editable-col.title-col input', function(e) {
            e.preventDefault();
            var title = $(this).val().trim()
                ,id = parseInt($(this).parents('tr').data('id'))
                ,product_item = self_object.getProductData(id);
            if (product_item) {
                product_item['title'] = title;
            }
        });

        // смена цены товара.
        body.on('change', 'td.editable-col.price-col input', function(e) {
            e.preventDefault();
            var price = parseFloat($(this).val().trim())
                ,id = parseInt($(this).parents('tr').data('id'))
                ,product_item = self_object.getProductData(id);
            if (product_item) {
                product_item['price'] = price;
            }
        });

        // открытие окна для выбора фото.
        body.on('click', '#pick-files', function(e) {
            e.preventDefault();
            $('.qq-upload-button input').click();
        });

        // модальное окно с информацией о товаре.
        body.on('click', 'a.vocabularies', function(e) {
            e.preventDefault();

            var id = parseInt($(this).data('id'))
                ,product_data = self_object.getProductData(id)
            ;

            if (product_data) {
                //do observable
                self_object.current_id = id;
                self_object.ko_vocabularies(product_data.vocabularies);
                self_object.ko_images(product_data.images);
            }

            self_object.clearUploadedBlock();
            self_object.getInitUploader();
            self_object.showProductModal();

            return this;
        });

        // модальное окно для привязки товаров к категории.
        body.on('click', 'button.product-category-relation', function(e) {
            e.preventDefault();
            var product_ids = self_object.getCheckedProductIds();
            if (product_ids.length == 0) {
                alert('Выберите товары для привязки к категории.');
                return false;
            }
            self_object.showProductCategoryModal();
        });

        // обновление списка категорий.
        body.on('click', 'a.update-category-options', function(e) {
            e.preventDefault();
            self_object.updateCategoryOptions();
        });

        // привязывание товаров к категории.
        body.on('click', 'button.tie-category', function(e) {
            e.preventDefault();
            var checked_products = self_object.getCheckedProductIds()
                ,selected_category = $('select[name=category] option:selected')
                ,category_id = selected_category.val()
                ,category_name = selected_category.text().replace(/-/g, '').trim()
            ;
            checked_products.forEach(function(product_id) {
                var product = self_object.getProductData(product_id);
                product['category'] = category_id;
                // set in table
                self_object
                    .getTableRow(product_id)
                    .find('.category-cell')
                    .html(category_name)
                ;
                self_object
                    .getTableRow(product_id)
                    .find('.product-checkbox')
                    .prop('checked', false)
                ;
            });
            self_object.closeProductCategoryModal();
        });

        // выбор чекбоксов.
        body.on('click', '.product-checkbox', function() {
            var product_checked = $('.product-checkbox:checked');
            if (product_checked.length > 1) {
                // массив уникальных названий товаров.
                var names = [];
                product_checked.each(function() {
                    names.push($(this).parents('tr:eq(0)').find('.title-col input').val());
                });
                names = names.filter(function (value, index, self) {
                    return self.indexOf(value) === index;
                });
                //TODO: отображать jBox с кнопкой объединить.
                //self_object.mergeVocabularies();

                // var button = $('<button class="merge_product">Объединить</button>').html();
                // $.notify('Объединить товары (объединяться характеристики)<br />' + button);
            }
        });
    },
    /**
     * Метод обновляет контрол с категориями на актуальный.
     */
    updateCategoryOptions: function() {
        var category_control_block = $('div.category-control-block');
        category_control_block.html('');
        $.ajax({url: '/import/get-category-control', type: 'POST'})
            .success(function(control_html) {
                category_control_block.html(control_html);
            })
            .error(function() {
               alert('Произошла ошибка, попробуйте позже!');
            });
    },
    /**
     * Метод возвращает массив id выбранных товаров.
     * @returns {Array}
     */
    getCheckedProductIds: function() {
        var product_ids = [];
        $('.product-checkbox').each(function() {
            if ($(this).is(':checked')) {
                product_ids.push(parseInt($(this).data('id')));
            }
        });
        return product_ids;
    },
    /**
     * Метод подчищает отображение загруженных фото.
     * @returns {PriceList}
     */
    clearUploadedBlock: function() {
        $('ul.qq-upload-list').html('');
        return this;
    },
    /**
     * Инициализирует загрузчик фото.
     * @returns {null}
     */
    getInitUploader: function () {
        var self_object = this;
        if (this.uploader === null) {
            //noinspection JSUnresolvedFunction,JSUnresolvedVariable
            this.uploader = new qq.FileUploader({
                element: document.getElementById('file-uploader'),
                action: '/api/uploader/qq-uploader',
                allowedExtensions: ['jpg', 'jpeg', 'png',],
                uploadButtonText: 'Загрузить фото с PC',
                failUploadText: 'Ошибка загрузки фото',
                inputName: 'file',
                onComplete: function(id, file_name, response_JSON) {
                    if (response_JSON.success) {
                        var image = new Image();
                        //noinspection JSUnresolvedVariable
                        image.setImage('/files/' + response_JSON.newFilename);
                        self_object.ko_images.push(image);
                    }
                    else {
                        alert('Произошла ошибка, попробуйте позже !');
                    }
                },
            });
        }
        return this.uploader;
    },
    /**
     * Метод пробегает по листу и добавляет observable свойства строками.
     * @returns {PriceList}
     */
    prepareForSave: function() {
        $.each(this.data, function(index, value) {
            if (value && value.vocabularies) {
                $.each(value.vocabularies, function(index, value) {
                    value.string_title = value.getTitle();
                    if (value.terms) {
                        $.each(value.terms, function(index, value) {
                            value.string_title = value.getTitle();
                        });
                    }
                });
            }
            if (value && value.images) {
                $.each(value.images, function(index, value) {
                    value.path = value.getImage();
                });
            }
        });
        return this;
    },
    addImage: function() {
        this.ko_images.push(new Image());
        return this;
    },
    getImages: function() {
        return this.ko_images;
    },
    /**
     * Сохранение данных прайс листа.
     * @param object
     * @param event
     */
    saveData: function(object, event) {
        event.target.setAttribute('disabled', 'disabled');
        this.prepareForSave();

        console.log(this.data);

        /*
        var loader = new FullPageLoader();
        loader.show();

        $.ajax({url: '/import/save-product-list', type: 'POST', data: {
            product_list: JSON.stringify(this.data),
            firm_id: parseInt($('input[name=firm_id]').val())
        }})
        .success(function(result) {
            event.target.removeAttribute('disabled');
            if (result.success) {
                loader.hide();
            }
            else {
                alert('Произошла ошибка, попробуйте позже !');
                loader.hide();
            }
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже !');
            loader.hide();
        });
        */
    },
    init: function(options) {
        //TODO: вынести в knockout.
        // обновление observable массива с не observable элементами.
        ko.observableArray.fn.refresh = function () {
            var data = this();
            this([]);
            this(data);
        };

        this.options = $.extend(this.options, options);
        this._bindEvents();
        ko.applyBindings(this);

        // //TODO
        // $(function() {
        //     $('button[type="submit"]').click();
        // });
        return this;
    }
};

$(function() {
    var import_settings_form = $('#import-params-form');
    PriceList.init({
        form: import_settings_form,
        table_selector: 'table.excel-table'
    });
});