{$form_start}

{$firm_id}
{$product_id}

    <fieldset>
        <div class="row">
            <div class="col-xs-3">
                {$part_size}
            </div>
            <div class="col-xs-3">
                {$request_validity}
            </div>
        </div>
        {* Контейнер для характеристик *}
        <div class="row">
            <div class="col-xs-6">
                {$vocabulary_terms_html}
            </div>
        </div>
        {* Контейнер для характеристик *}
    </fieldset>
    <fieldset>
        <legend>Адрес поставки</legend>
        {* Регион *}
        {$region}
        {* Адрес *}
        {$address}
    </fieldset>
{$form_end}