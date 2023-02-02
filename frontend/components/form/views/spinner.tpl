<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}</div>
    {/if}
    <div class="spinner">
        <a role="button" data-action="decrease" class="spinner-btn spinner-btn-minus"></a>
        <label>
            {$control}
        </label>
        <a role="button" data-action="increase" class="spinner-btn spinner-btn-plus"></a>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>