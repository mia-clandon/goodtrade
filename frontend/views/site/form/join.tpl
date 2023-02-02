{$form_start}

<section>
  <h3 class="sub-title">Информация о компании</h3>
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

        <fieldset>
          <legend>Дополнительная информация</legend>
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
    </fieldset>
</section>

<div id="register-bottom">
    <!--    <a id="register-prev">Назад</a>-->
    <button type="submit" id="register-next" class="btn btn-blue pull-right">Зарегистрироваться</button>
</div>

{$form_end}