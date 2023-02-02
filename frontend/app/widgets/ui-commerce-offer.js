/**
 * Работа с коммерческим запросом.
 */

import './ui-popup';
import './ui-spinner';
import './ui-select';
import './ui-input-city';
import './ui-choice';
import './ui-checkbox';

export default $.widget('ui.commerce', $.ui.popup, {
    options: {
        product_id: 0,
        /** товары на которые был отправлен коммерческий запрос. */
        requested_product_ids: [],
        /** элемент при нажатии на который - будет открываться popup. */
        toggleSelector: '.popup-toggle.commercial-popup',
        loaded_form: 0,
        resend: 0,
    },
    _create: function () {
        this._super();
        this.options.resend = $(this.options.toggleSelector).data('resend')===1?1:0;
    },
    _init: function () {
        this._super();
        let $root = this.element,
            $body = $('body'),
            th = this;
        // закрытие модального окна.
        $body.on('click', '#popup-cancel', function () {
            th.cancel();
        });
        // отправка запроса.
        $body.on('click', '#popup-send', function () {
            th.send();
        });
        // запрос отправлен.
        $body.on('form.saved', th.element.find('form').selector, function (event, params) {
            th.requestSuccess(params.data);
        });
    },
    requestSuccess: function (data) {
        this.showCongratulationsModal(data.request_validity);
        $(this.options.toggleSelector).addClass('is-success');
    },
    /**
     * Метод отображает окно после отправки коммерческого запроса.
     * @return {ui.commerce}
     */
    showCongratulationsModal: function (request_validity) {
        let $form = this.element.find('form'),
            $popup_head = this.element.find('.popup-head'),
            $popup_foot = this.element.find('.popup-foot')
            ;
        $form.remove();
        $popup_head.addClass('is-hidden');
        $popup_foot.addClass('is-hidden');

        this.element.find('.commercial-request-success .request-validity').text(request_validity);
        this.element.find('.commercial-request-error').hide();
        this.element.find('.commercial-request-success').show();
        this.element.siblings('.commercial-popup').removeClass().addClass('btn btn-disabled').html('<span>Ожидается ответ ('+request_validity+' ДНЕЙ)</span>');
        if($popup_head.hasClass('commercial-request-all')) {
            window.location.reload();
        }
        return this;
    },
    /**
     * Отправка коммерческого запроса.
     * @returns {boolean}
     */
    send: function () {
        let form = this.element.find('form');
        form.submit();
        return true;
    },
    show: function () {
        // загрузка содержимого для создания коммерческого запроса.
        this.setProductId(this.options.data.id);
        let $root = this.element
            , product_id = this.getProductId()
            , th = this
        ;
        // форма коммерческого запроса уже загружена.
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

            // инициализация скриптов контролов формы.
            $root.find('.choice').choice();
            $root.find('.ui-checkbox').checkbox();
            $root.find('.spinner').spinner();
            $root.find('input[name=region]').parents('div:first').inputCity();
            $root.find('.select').select();

            //todo: вставить срок действия коммерческого запроса в модалку с ответом.
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