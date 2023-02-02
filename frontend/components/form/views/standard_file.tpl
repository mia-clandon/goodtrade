<div class="form-control">
    <div class="form-control-label">{$label}
        {if $label_tip}
            <span class="form-control-label-tip">{$label_tip}</span>
        {/if}
    </div>
    <div class="input {$input_type}">
        {$control}
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    {if $errors|@count gt 0}
        <div class="form-control-message">{$errors_string}</div>
    {/if}
</div>