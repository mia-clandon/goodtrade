<div class="ui-checkbox">
    {$control}
    {if $label}
        <label class="ui-checkbox-label" for="{$control_id}">{$label}</label>
    {/if}
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message form-control-message_chechbox"></div>
</div>