/**
 * Класс работающий с AJAX формами.
 * @type {{form: null, error_renderer: null, setForm: AppForms.setForm, setErrorRenderer: AppForms.setErrorRenderer, getErrorRenderer: AppForms.getErrorRenderer, init: AppForms.init, validate: AppForms.validate, getAppEnv: AppForms.getAppEnv}}
 */
let AppForms = {
    form: null,
    error_renderer: null,
    options: {
        // кеш текста кнопки.
        submit_text: '',
    },
    setForm: function(form) {
        this.form = form;
        return this;
    },
    setErrorRenderer: function(error_renderer) {
        this.error_renderer = error_renderer;
        return this;
    },
    getErrorRenderer: function () {
        return this.error_renderer;
    },
    init: function() {
        if (this.getAppEnv() === 'frontend') {
            if (this.form !== null
                && (this.form.data('b2b') === 'true' || this.form.data('b2b'))
            ) {
                this.setErrorRenderer(FrontendErrorsRendererB2B);
            }
            else {
                this.setErrorRenderer(FrontendErrorsRenderer);
            }
        }
        else {
            this.setErrorRenderer(BackendErrorsRenderer);
        }
        return this;
    },
    validate: function(event) {
        let th = this
            ,form = th.form
        ;
        if (form !== null && form.data('ajax-form') == true) {
            event.preventDefault();
            let url = form.attr('action');
            // очистка ошибок
            this.getErrorRenderer().clearErrors(form);
            this.beforeFormSend(form);
            let request = th.serializeArray();
            $.ajax({
                url: url,
                data: request,
                type: 'POST',
                success: function (data) {
                    if (data.errors !== undefined) {
                        th.getErrorRenderer()
                            .setForm(form)
                            .render(data.errors);
                        form.trigger("form.errors", {errors: data.errors});
                    }
                    else {
                        if (data.success !== undefined && data.success === true) {
                            // вызывает событие после успешного сохранения формы.
                            form.trigger("form.saved", {data: data, request: request});
                        }
                    }
                }
            })
            .done(function() {
                // возвращаю submit элемент в нормальное состояние.
                th.setSubmitNormalizeState(form);
                form.trigger("form.done");
            })
            .fail(function (response) {
                // что то пошло не так - сервер ругается.
                console.log(response.statusText);
                $.notify("Произошла ошибка, попробуйте позже !", "error");
            });
        }
    },
    /**
     * Выполняется перед отправкой формы на сервер.
     * @param form
     */
    beforeFormSend: function (form) {
        this.setSubmitWaitState(form);
    },
    /**
     * Меняет состояние submit элемент формы в wait;
     * @param form
     * @return {AppForms}
     */
    setSubmitWaitState: function (form) {
        let $submit_element = form.find('[type=submit]');
        if ($submit_element.length > 0) {
            this.options.submit_text = $submit_element.html();
            let $element = $submit_element;
            // если текст кнопки лежит в span теге.
            if ($submit_element.find('span').length > 0) {
                $element = $submit_element.find('span');
            }
            $element.text('Подождите ...').attr('disabled', 'disabled');
        }
        return this;
    },
    /**
     * Меняет состояние submit элемент формы в нормальное состояние;
     * @param form
     * @return {AppForms}
     */
    setSubmitNormalizeState: function (form) {
        let $submit_element = form.find('[type=submit]');
        if ($submit_element.length > 0) {
            $submit_element.html(this.options.submit_text).removeAttr('disabled');
        }
        return this;
    },
    getAppEnv: function() {
        if (window.location.hostname.indexOf('dashboard') == 0) {
            return 'backend';
        }
        return 'frontend';
    },
    serializeArray: function() {
        let values = this.form.serializeArray();

        // так как не serializeArray не добавляет disabled input's
        values = values.concat(
            this.form.find(':input:disabled').map(
                function() {
                    return {name: this.name, value: this.value}
                }).get()
        );

        // так как не serializeArray не добавляет not checked checkbox'ы
        values = values.concat(
            this.form.find('input[type=checkbox]:not(:checked)').map(
                function() {
                    return {name: this.name, value: 0}
                }).get()
            );

        return values;
    }
};
/**
 * Рендерер ошибок для Frontend окружения.
 * @type {{render: FrontendErrorsRenderer.render, clearErrors: FrontendErrorsRenderer.clearErrors}}
 */
let FrontendErrorsRenderer = {
    form: null,
    render: function(errors) {
        let form = this.form,
            self_object = this;

        $.map(errors, function (errors, component) {

            let $component = form.find('[name=' + component + ']');
            if (!$component.length) {
                $component = form.find('[name^="' + component + '["]:first');
            }
            let $form_control = $component.parents('.form-control');

            // Чекбокс ?
            let is_checkbox = $component.attr('type') == 'checkbox' || $component.hasClass('hidden-checkbox');
            // Группа инпутов ?
            let is_group = $component.parents('.multiply').length > 0;
            // Сфера деятельности ?
            let is_category = $form_control.children('.ui-category').length > 0;

            // для того чтобы чекбокс был красным.
            if (is_checkbox) {
                $component.parents('.ui-checkbox').addClass('is-required');
            }
            else if (is_group) {
                let $group = $component.parents('.multiply');
                $group.addClass('has-error');
                self_object.addErrors($form_control, '.form-control-message.common', errors);
            }
            else if (is_category) {
                $form_control.children('.ui-category').addClass('has-error');
                self_object.addErrors($form_control, '.form-control-message', errors);
            }
            else {
                $component.parent().addClass('has-error');
                self_object.addErrors($form_control, '.form-control-message', errors);
            }
        });
        return this;
    },
    addErrors: function ($form_control, selector, errors) {
        // собираю все ошибки и вставляю в контрол.
        $.map(errors, function(error) {
            $form_control.find(selector)
                .append('<span>'+ error +'</span>');
        });
        return this;
    },
    clearErrors: function(form) {
        let $control_span_errors = form.find('.form-control-message');
        $control_span_errors.parent().removeClass('has-error');
        $control_span_errors.html('');
        return this;
    },
    setForm: function(form) {
        this.form = form;
        return this;
    }
};

/**
 * Рендерер ошибок для Frontend окружения(новая верстка).
 * @type {{render: FrontendErrorsRendererB2B.render, clearErrors: FrontendErrorsRendererB2B.clearErrors}}
 */
let FrontendErrorsRendererB2B = {
    form: null,
    render: function(errors) {
        let form = this.form,
            self_object = this;

        $.map(errors, function (errors, component) {

            let $component = form.find('[name=' + component + ']');
            if (!$component.length) {
                $component = form.find('[name^="' + component + '["]:first');
            }

            let $parent = $component.parents('.control-wrapper:first');
            if ($parent.length) {
                $parent.addClass('has-error').addClass('input_danger');
            }

            let $form_control = $component.parents('.form-control');
            self_object.form.find('.form-control__message').html('');

            // селектор контрола для ошибки.
            let control_message_selector = '.form-control__message';
            if ($(control_message_selector).length === 0) {
                control_message_selector = '.form-control__bottom-text';
            }
            self_object.addErrors($form_control, control_message_selector, errors);
        });
        return this;
    },
    addErrors: function ($form_control, error_block_selector, errors) {
        // собираю все ошибки и вставляю в контрол.
        $.map(errors, function(error) {
            $form_control.find(error_block_selector)
                .append('<span>'+ error +'</span>')
                .addClass('form-control__message_danger');
        });
        return this;
    },
    clearErrors: function(form) {

        // очистка ошибок.
        form.find('.form-control__bottom-text').html('');

        /*
        form.find('.input_danger').removeClass('input_danger');
        form.find('.form-control__bottom-text').html();
        form.find('.form-control_required').removeClass('form-control_required');
        form.removeClass('.form-control_required');
        form.find('.form-control__message_danger').removeClass('form-control__message_danger');
        let $control_span_errors = form.find('.form-control-message');
        $control_span_errors.html('');
        */
        return this;
    },
    setForm: function(form) {
        this.form = form;
        return this;
    }
};
/**
 * Рендерер ошибок для Backend окружения.
 * @type {{render: BackendErrorsRenderer.render, clearErrors: BackendErrorsRenderer.clearErrors}}
 */
let BackendErrorsRenderer = {
    form: null,
    render: function(errors) {
        let form = this.form
            ,th = this;
        $.map(errors, function (errors, component) {
            let $component = form.find('[name=' + component + ']');
            if ($component.length === 0) {
                $component = form.find('[name="' + component + '[]"]');
            }
            // вывод ошибок для copy контрола (контрол - коллекция).
            if ($component.parents('div.copy-control').length > 0) {
                if ($.isArray(errors) && errors.length > 0) {
                    // ошибки для каждого компонента коллекции.
                    errors.forEach(function(control_error_list, iterator) {
                        let $collection_component = $component
                            .parents('div.copy-control:first')
                            .find('.collection-control').eq(iterator);
                        th.renderComponentErrors($collection_component, control_error_list);
                    });
                }
            }
            // вывод ошибок для остальных контролов.
            else {
                th.renderComponentErrors($component, errors);
            }
        });
        return this;
    },
    renderComponentErrors: function ($component, errors) {
        $component.addClass('error');
        let error_string = '';
        $.map(errors, function (error) {
            error_string += ' ' + error;
        });
        $('<span />', {class: 'help-block form-error', style: 'color: RED;'})
            .text(error_string.trim())
            .insertAfter($component)
        ;
        return this;
    },
    clearErrors: function(form) {
        form.find('span.form-error').remove();
        return this;
    },
    setForm: function(form) {
        this.form = form;
        return this;
    }
};

$(function() {
    $('body').on('submit', 'form', function(e) {
        AppForms.setForm($(this)).init().validate(e);
    });
});