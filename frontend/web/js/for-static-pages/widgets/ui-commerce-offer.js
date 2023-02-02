/**
 * Работа с коммерческим запросом.
 */
define([
    'jquery',
    'jquery-ui',
    'widgets/ui-popup',
    'widgets/ui-spinner',
    'widgets/ui-select',
    'widgets/ui-input-city',
    'widgets/ui-choice',
    'widgets/ui-checkbox',

], function ($) {
    return $.widget('ui.commerce', $.ui.popup, {

        options: {
            product_id: 0,
            /** товары на которые был отправлен коммерческий запрос. */
            requested_product_ids: [],
            /** элемент при нажатии на который - будет открываться popup. */
            toggleSelector: '.popup-toggle.commercial-popup',
            loaded_form: 0,
        },
        _create: function () {
            this._super();
        },
        _init: function () {
            this._super();
            var $root = this.element,
                $body = $('body'),
                th = this;
            // закрытие модального окна.
            $body.on('click', '#popup-cancel', function() {
                th.cancel();
            });
            // отправка запроса.
            $body.on('click', '#popup-send', function() {
                th.send();
            });
            // запрос отправлен.
            $body.on('form.saved', th.element.find('form').selector, function() {
                th.requestSuccess();
            });
        },
        requestSuccess: function() {
            this.showCongratulationsModal();
        },
        /**
         * Метод отображает окно после отправки коммерческого запроса.
         * @return {ui.commerce}
         */
        showCongratulationsModal: function () {
            var $form = this.element.find('form'),
                $popup_head = this.element.find('.popup-head'),
                $popup_foot = this.element.find('.popup-foot')
            ;
            $form.remove();
            $popup_head.addClass('is-hidden');
            $popup_foot.addClass('is-hidden');

            this.element.find('.commercial-request-success').show();
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
                ,product_id = this.getProductId()
                ,th = this
            ;
            // форма коммерческого запроса уже загружена.
            if (this.options.loaded_form == product_id) {
                return false;
            }
            $.ajax({
                url: '/commercial/get-request-form',
                type: 'POST',
                data: {product_id: product_id}
            })
            .done(function(form) {
                th.options.loaded_form = product_id;
                th.element.html(form);

                // инициализация скриптов контролов формы.
                $root.find('.choice').choice();
                $root.find('.ui-checkbox').checkbox();
                $root.find('.spinner').spinner();
                $root.find('input[name=region]').parents('div:first').inputCity();
                $root.find('.select').select();
            })
            .fail(function() {
                alert('Произошла ошибка, попробуйте позже.');
            });
        },
        close: function () {},
        setProductId: function (product_id) {
            this.options.product_id = product_id;
            return this;
        },
        getProductId: function () {
            return this.options.product_id;
        }
    });
});