{$form_start}
    <fieldset>
        <legend>Сменить пароль</legend>
        {$current_password}
        <div class="row">
            <div class="col-xs-3">
                {$new_password}
            </div>
            <div class="col-xs-3">
                {$re_password}
            </div>
        </div>
    </fieldset>
    {$submit}
{$form_end}