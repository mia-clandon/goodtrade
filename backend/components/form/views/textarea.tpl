<div class="form-group">
    {if $label}
    <label>{$label}</label>
    {/if}
    {$control}
    {if $errors|@count gt 0}
        <span class="help-block">{$errors_string}</span>
    {/if}
</div>