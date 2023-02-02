{$form_start}

<section>
    <fieldset>

        {* Название компании / БИН *}
        <div class="form-control-group company-title-bin-group">
            {$company_title}
            {$company_bin}
        </div>

        <div class="company-activity-wrap">
            {* Сфера деятельности *}
            {$company_activity}
        </div>

        <fieldset>
            <legend>Контактные данные</legend>
            <div data-step-skip="true" class="step">
                {* Регион *}
                {$company_location}
            </div>
            <div data-step-skip="true">
                {* Юридический адрес *}
                {$company_legal_address}
            </div>
        </fieldset>

        {* Телефон. *}
        {$company_phone}

        {* Email *}
        {$company_email}

        <div class="collapse js-collapse">
            <a role="button" href="#" class="collapse-toggle js-collapse-toggle">Дополнительная информация</a>
            <div class="collapse-content js-collapse-content">
                <fieldset>
                    {* Фото организации *}
                    {$company_image}
                    {* Описание организации *}
                    {$company_text}
                </fieldset>
                <fieldset>
                    <legend>Реквизиты</legend>
                    {* Банк *}
                    {$company_bank}
                    {* БИК *}
                    {$company_bik}
                    {* ИИК *}
                    {$company_iik}
                    <div class="row">
                        <div class="col-xs-3">
                            {* КБЕ *}
                            {$company_kbe}
                        </div>
                        <div class="col-xs-3">
                            {* КНП *}
                            {$company_knp}
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </fieldset>
</section>

{$submit}

{$form_end}