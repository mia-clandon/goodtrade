/**
 * Контрол Select с плагином Selectize.
 */
export default class SelectizeControl {
    /**
     * Инициализация контрола.
     */
    init() {
        $('select[class^=selectize-select-]').each(function() {
            let params = $(this).data('params');

            // ... старый код ...
            let $disabled_options = $(this).find('option[disabled=disabled]')
                ,disabled_options = []
            ;
            $disabled_options.each(function() {
                disabled_options.push($(this).val());
            });

            let can_create_options = $(this).data('can-create-options')
                ? parseInt($(this).data('can-create-options')) : 0;
            // ... старый код ...

            let plugins =  {
                plugins: {
                    'disable_options': {
                        disableOptions: disabled_options
                    }
                }
            };
            params = $.extend(params, plugins);
            $(this).selectize(params);
        });
    }
}

$(function () {
    new SelectizeControl().init();
});