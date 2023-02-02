define(['jquery', 'jquery-ui', 'widgets/ui-spinner', 'widgets/ui-select', 'widgets/ui-input-city'], function ($) {
    return $.widget('ui.commerce', {
        options: {
            url: '../public/api/commerce.json',
            form: null,
            spinner: null,
            select: null,
            hidden: null,
            input: null,
            jxhr: null,
            visible: false,
            loading: false,
            success: false,
            verticalAlign: 'top',
            horizontalAlign: 'left',
            id: 0,
            /** товары на которые был отправлен коммерческий запрос. */
            requested_product_ids: [],
        },
        _create: function () {
            var $root = this.element,
                $cancel = $root.find('#popup-cancel'),
                $send = $root.find('#popup-send'),
                $toggle = $('.popup-toggle');
            this.options.spinner = $root.find('.spinner');
            this.options.select = $root.find('.select');
            this.options.input = $root.find('.input');
            this.options.hidden = $('<input/>', {'type': 'hidden'});
            this.options.form = $root.find('form');
            this._on($send, {'click': this.send});
            this._on($cancel, {'click': this.cancel});
            this._on($toggle, {'click': this._handleClick});
            //this._on(this.options.form, {'form.saved': this._formSaved});
        },
        _init: function () {
            var $root = this.element,
                $spinner = this.option('spinner'),
                $select = this.option('select'),
                $hidden = this.option('hidden'),
                $form = this.option('form'),
                $input = this.option('input'),
                self_object = this;
            this.options.spinner = $spinner.spinner();
            this.options.select = $select.select();
            this.options.input = $input.inputCity();
            $hidden.appendTo($root);
            $form.on('form.saved', function() {self_object.formSaved();});
            this._on($form, {
                'spinnerchange': this._handleChange,
                'selectchange': this._handleChange,
            });
        },
        /**
         * Callback отправки коммерческого запроса.
         * @return {ui.commerce}
         */
        formSaved: function() {
            this.options.requested_product_ids.push(
                this.getProductId()
            );
            this.showCongratulationsModal();
            return this;
        },
        /**
         * Метод прячет окно после отправки коммерческого запроса, и отображает форму.
         * Обратный метод @see ui.commerce.showCongratulationsModal
         * @return {ui.commerce}
         */
        showCommercialRequestForm: function () {
            var $form = this.option('form'),
                $popup_head = this.element.find('.popup-head'),
                $popup_foot = this.element.find('.popup-foot')
            ;

            $form.removeClass('is-hidden');
            $popup_head.removeClass('is-hidden');
            $popup_foot.removeClass('is-hidden');

            $('.commercial-request-success').hide();
            return this;
        },
        /**
         * Метод отображает окно после отправки коммерческого запроса.
         * @return {ui.commerce}
         */
        showCongratulationsModal: function () {
            var $form = this.option('form'),
                $popup_head = this.element.find('.popup-head'),
                $popup_foot = this.element.find('.popup-foot')
            ;

            $form.addClass('is-hidden');
            $popup_head.addClass('is-hidden');
            $popup_foot.addClass('is-hidden');

            $('.commercial-request-success').show();
            return this;
        },
        _destroy: function () {
            var $spinner = this.option('spinner'),
                $select = this.option('select'),
                $hidden = this.option('hidden'),
                $input = this.option('input');
            $spinner.spinner('destroy');
            $select.select('destroy');
            $input.inputCity('destroy');
            $hidden.remove();
        },
        refresh: function () {
            var $form = this.option('form'),
                $spinner = this.option('spinner'),
                $input = this.option('input'),
                $hidden = this.option('hidden'),
                $jxhr = this.option('jxhr'),
                $select = this.option('select'),
                id = this.option('id');
            $form.find('.has-error')
                .removeClass('has-error');
            $spinner.spinner('refresh');
            $select.select('refresh');
            $hidden.val(id);
            if ($jxhr && 'abort' in $jxhr) {
                $jxhr.abort();
            }
        },
        /**
         * Валидация.
         * @deprecated
         * @returns {boolean}
         */
        validate: function () {
            //TODO: отключил использование валидации в связи с валидацией jquery.validate и серверная валидация.
            var $spinner = this.option('spinner'),
                $select = this.option('select'),
                $input = this.option('input'),
                isValid = true;
            if ($spinner.spinner('option', 'value') <= 0) {
                $spinner.addClass('has-error');
                isValid = false;
            }
            else {
                $spinner.removeClass('has-error');
            }
            if ($select.select('option', 'value')) {
                $select.removeClass('has-error');
            }
            else {
                $select.addClass('has-error');
                isValid = false;
            }
            $input.each(function () {
                var $self = $(this);
                if ($self.inputCity('option', 'value')) {
                    $(this).removeClass('has-error');
                }
                else {
                    $(this).addClass('has-error');
                    isValid = false;
                }
            });
            return isValid;
        },
        /**
         * Отправка коммерческого запроса.
         * @returns {boolean}
         */
        send: function () {
            var form = this.options.form;
            form.submit();
            return true;
        },
        /**
         * Закрытие модального окна с коммерческим запросом.
         */
        cancel: function () {
            this.refresh();
            this.option({
                visible: false
            });
        },
        /**
         * @deprecated
         * @param e
         * @param data
         * @private
         */
        _handleChange: function (e, data) {
            if (data.value) {
                $(e.target).removeClass('has-error');
            }
        },
        /**
         * Открытие модального окна.
         * @param e
         * @private
         */
        _handleClick: function (e) {
            var $root = this.element,
                $el = $(e.currentTarget),
                $wrap = $el.closest('.popup-dropdown');
            $wrap.append($root);
            this.options.id = parseInt($el.data('id'));
            this.option({
                verticalAlign: $el.data('vertical-align'),
                horizontalAlign: $el.data('horizontal-align'),
                visible: true,
                id: $el.data('id')
            });
            e.stopPropagation();
        },
        /**
         * Метод возвращает id товара для которого отправляют коммерческий запрос.
         * @returns {Number}
         */
        getProductId: function() {
            return parseInt(this.options.id);
        },
        /**
         * Открытие формы коммерческого запроса.
         */
        openForm: function() {
            // проверяю, был ли уже запрос к товару.
            if (
                this.options.requested_product_ids.indexOf(this.getProductId()) !== -1
                || this.element.hasClass('has-request')
            ) {
                // коммерческий запрос уже был послан.
                this.showCongratulationsModal();
            }
            else {
                // коммерческий запрос еще не посылали.
                this.loadVocabularyTerms();

                // показываю форму.
                this.showCommercialRequestForm();
            }
            return this;
        },
        /**
         * Метод загружает характеристики товара.
         */
        loadVocabularyTerms: function() {
            var product_id = this.getProductId()
                ,vocabulary_container = this.element.find('.product-vocabulary-terms');

            $.ajax({url: '/product/get-product-vocabulary-terms', data: {product_id: product_id}, type: 'POST'})
                .done(function(vocabulary_terms) {
                    vocabulary_container.html(vocabulary_terms);
                    $('.choice').choice();
                    $('.ui-checkbox').checkbox();
                })
                .fail(function() {
                    alert('Произошла ошибка, попробуйте позже.');
                })
            ;
        },
        /**
         * Метод удаляет загруженные характеристики.
         * @return {ui.commerce}
         */
        clearVocabularyTerms: function() {
            var vocabulary_container = this.element.find('.product-vocabulary-terms');
            vocabulary_container.html('<img src="/img/preloader-32.gif">');
            return this;
        },
        /**
         * Очистка формы от предъустановленных значений.
         * @return {ui.commerce}
         */
        clearForm: function () {
            // очистка подгруженных значений.
            this.clearVocabularyTerms();
            // очистка формы.
            var $form = this.option('form');
            if ($form.length > 0) {
                $form[0].reset();
            }
            return this;
        },
        /**
         * Установка опций.
         * @param key
         * @param val
         * @returns {*}
         * @private
         */
        _setOption: function (key, val) {
            var $root = this.element,
                $hidden = this.option('hidden'),
                $self = this;
            if (key == 'verticalAlign') {
                switch (val) {
                    case 'top' :
                        $root.css({top: '0px', bottom: 'auto'});
                        break;
                    case 'bottom' :
                        $root.css({bottom: '0px', top: 'auto'});
                        break;
                    default :
                        $root.css({top: '0px', bottom: 'auto'});
                        break;
                }
            }
            else if (key == 'horizontalAlign') {
                switch (val) {
                    case 'left' :
                        $root.css({left: '0px', right: 'auto'});
                        break;
                    case 'right' :
                        $root.css({right: '0px', left: 'auto'});
                        break;
                    default :
                        $root.css({left: '0px', right: 'auto'});
                        break;
                }
            }
            else if (key == 'visible') {
                if (val) {
                    this.openForm();
                    this._removeClass('is-hidden');
                    $('body').on('click', function (e) {
                        var $el = $(e.target);
                        if ($root.is($el) || $root.has($el).length) {
                            return false;
                        }
                        else {
                            if (!$self.option('disabled')) {
                                $self.option('visible', false);
                            }
                            $('body').off(e);
                        }
                    });
                }
                else {
                    this.clearForm();
                    this._addClass('is-hidden');
                }
            }
            else if (key == 'id') {
                if ($.isNumeric(val)) {
                    $hidden.val(val);
                }
            }
            return this._super(key, val);
        }
    });
});