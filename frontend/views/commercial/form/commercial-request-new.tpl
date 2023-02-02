{$form_start}

{$firm_id}
{$product_id}

    <div class="row">
        <div class="col">
            {*Размер партии.*}
            {$part_size}
        </div>
        <div class="col">
            {*Выбор срока коммерческого запроса.*}
            {$request_validity}
        </div>
    </div>

    <fieldset>
        <legend>Адрес поставки</legend>
        {* Регион *}
        {$region}
        {* Адрес *}
        {$address}
        {* Использовать адрес компании в качестве адреса поставки *}
        {$use_mine_address}
    </fieldset>

    <div class="form-control">
        <button data-action="popup-send" class="button button_primary">
            <span class="button__text">Отправить запрос</span>
        </button>
        <button data-action="popup-close" class="button button_small button_link">
            <span class="button__text">Отменить</span>
        </button>
    </div>

{$form_end}