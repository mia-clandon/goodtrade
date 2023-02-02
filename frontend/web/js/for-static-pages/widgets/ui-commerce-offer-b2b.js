/**
 * Работа с коммерческим запросом.
 */
define([
    'jquery',
    'jquery-ui',
    'widgets/ui-popup',
    'widgets/ui-select-b2b',
    //'widgets/ui-input-city',
    //'widgets/ui-choice',
    'widgets/ui-checkbox',

], function ($) {
    return $.widget('ui.commerce', $.ui.popup, {
        options: {
            product_id: 0,
            /** товары на которые был отправлен коммерческий запрос. */
            requested_product_ids: [],
            loaded_form: 0,
            resend: 0,
            /* элемент, при нажатии на который будет открываться всплывающее окно */
            toggleSelector: '[data-action="commerce-popup"]',
            // элемент, внутри которого находится переключатель и будет появляеться модальное окно
            wrapperSelector: '[data-type="popup-wrapper"]',
            // элемент, при нажатии на который происходит событие отмены.
            cancelSelector: '[data-action="popup-close"]',
            // элемент, при нажатии на который происходит событие отправки.
            sendSelector: '[data-action="popup-send"]',

            selectors: {
                successMessage: '.modal__notice_success',
                requestValidity: '.modal__notice_success [data-type="request-validity"]', // Срок действия комм. предложения
                regionInput: '.input input[data-type="company-region"]',
                addressInput: '.input input[data-type="company-address"]',
                locationCheckbox: '.checkbox input[data-type="location"]'
            }
        },
        _create: function () {
            this._super();
            this.options.resend = $(this.options.toggleSelector).data('resend')===1?1:0;
        },
        _init: function () {
            this._super();
            var $root = this.element,
                $body = $('body'),
                th = this;
            // закрытие модального окна.
            $body.on('click', th.options.cancelSelector, function () {
                th.cancel();
            });
            // отправка запроса.
            $body.on('click', th.options.sendSelector, function () {
                th.send();
            });
            // запрос отправлен.
            $body.on('form.saved', th.element.find('form'), function (event, params) {
                th.requestSuccess(params.request);
            });
        },
        requestSuccess: function (request) {
            this.showCongratulationsModal(request[4].value);
            $(this.options.toggleSelector).addClass('is-success');
        },
        /**
         * Метод отображает окно после отправки коммерческого запроса.
         * @return {ui.commerce}
         */
        showCongratulationsModal: function (request_validity) {
            this.element.find('form').remove();
            this.element.find(this.options.selectors.requestValidity).text(request_validity);
            this.element.find(this.options.selectors.successMessage).show();
            return this;
        },
        /**
         * Отправка коммерческого запроса.
         * @returns {boolean}
         */
        send: function () {
            var form = this.element.find('form');
            form.submit();
            return true;
        },
        show: function () {
            // загрузка содержимого для создания коммерческого запроса.
            this.setProductId(this.options.data.id);
            var $root = this.element
                , product_id = this.getProductId()
                , th = this
            ;

            if (this.options.loaded_form == product_id && this.options.resend == 0) {
                return false;
            }

            this.element.html('');

            $.ajax({
                url: '/commercial/get-request-form',
                type: 'POST',
                data: {product_id: product_id, resend: this.options.resend}
            })
                .done(function (form) {
                    th.options.loaded_form = product_id;
                    th.element.html(form);

                    let $regionInput = $root.find(th.options.selectors.regionInput),
                        $addressInput = $root.find(th.options.selectors.addressInput),
                        $locationCheckbox = $root.find(th.options.selectors.locationCheckbox),
                        $tips = $regionInput.nextAll(th.options.selectors.tips);

                    // инициализация скриптов контролов формы.
                    //$root.find('.choice').choice();
                    $root.find('.checkbox').checkbox({ checkedClass: "checkbox_checked" });
                    $regionInput.parent().inputCity({
                        marginTopBeforeAnim: $tips.css('margin-top') * 2,
                        marginTopAfterAnim: $tips.css('margin-top'),
                        tipsClass: 'tips__list',
                        selectors: {
                            tips: '.tips',
                            tipsBody: '.tips-body',
                            inputField: '.input-field',
                        }
                    });
                    $root.find('.range_value').range({ type: 'value' });
                    $root.find('.select').select();

                    if ($locationCheckbox.length && $locationCheckbox[0].checked) {
                        $regionInput.prop("disabled", true);
                        $addressInput.prop("disabled", true);
                        $regionInput.parent(".input").addClass("input_disabled");
                        $addressInput.parent(".input").addClass("input_disabled");
                    }

                    $locationCheckbox.on("click", function() {
                        if(this.checked) {
                            $regionInput.prop("disabled", true);
                            $addressInput.prop("disabled", true);
                            $regionInput.parent(".input").addClass("input_disabled");
                            $addressInput.parent(".input").addClass("input_disabled");
                        } else {
                            $regionInput.prop("disabled", false);
                            $addressInput.prop("disabled", false);
                            $regionInput.parent(".input").removeClass("input_disabled");
                            $addressInput.parent(".input").removeClass("input_disabled");
                        }
                    });

                    // todo: вставить срок действия коммерческого запроса в модалку с ответом.
                    // А разве выше этого не произошло с элементом .request-validity в методе showCongratulationsModal?
                })
                .fail(function (response) {
                    console.log(response.statusText);
                    $.notify("Произошла ошибка, попробуйте позже !", "error");
                });
        },
        close: function () {
        },
        setProductId: function (product_id) {
            this.options.product_id = product_id;
            return this;
        },
        getProductId: function () {
            return this.options.product_id;
        }
    });
});