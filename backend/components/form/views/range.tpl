<div class="form-group range-wrapper">
    {if $label}
        {$label}
    {/if}
    <span class="current-value">текущее значение: <span class="value">{$value_header}</span>.</span>
    <div class="row">
        <div class="left col-sm-2">
             <span>от {$from}</span>
        </div>
        {$hidden_control}
        {$control}
        <div class="right col-sm-2">
             <span>до {$to}</span>
        </div>
    </div>
    {$is_double_checkbox}
    {if $errors|@count gt 0}
        <span class="help-block">{$errors_string}</span>
    {/if}
</div>