<div class="form-control">
    {if $label}<div class="form-control-label">{$label}</div>{/if}
    <div class="input">
        {$control}
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>