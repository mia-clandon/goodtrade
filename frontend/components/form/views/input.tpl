<div class="form-control">
    <div class="form-control-label">{$label}
        {if $label_tip}
            <span class="form-control-label-tip">{$label_tip}</span>
        {/if}
    </div>
    <div class="control-wrapper input {$input_type}">
        {$control}
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>