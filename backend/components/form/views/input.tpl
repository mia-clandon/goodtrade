<div class="form-group">
    {if $label}
        {$label}
    {/if}
    {if $control_col_width}
        <div class="{$control_col_width}">
    {/if}
            {$control}
    {if $control_col_width}
        </div>
    {/if}
    {if $errors|@count gt 0}
        <span class="help-block">{$errors_string}</span>
    {/if}
</div>