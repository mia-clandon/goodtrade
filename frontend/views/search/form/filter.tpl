{* @todo: пока без контролов т.к. нет времени. *}
{$form_start}

<fieldset class="col-8">
    <legend>{$form_title}</legend>

    {$price}
    {$capacities}
    {$terms}
    {$submit}

    {* todo: переделать на контролы, т.к. верхний $submit состоит из одной лишь кнопки *}
    {*
    <div class="form-control">
        <button class="button button_primary">
            <span class="button__text">Применить фильтр</span>
        </button>
        <button class="button button_small button_link" data-action="popup-close">
            <span class="button__text">Сбросить</span>
        </button>
    </div>
    *}

    {* todo: переделать на контрол *}
    {*
    <div class="form-control">
        <div class="form-control__top-text">
            <div class="form-control__label">Регион реализации</div>
        </div>
        <div class="input">
            <input type="text" placeholder="Весь Казахстан">
        </div>
    </div>*}

</fieldset>

{$additional_filter_params}
{$form_end}