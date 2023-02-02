<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}</div>
    {/if}
    <ul class="choice">
        {foreach from=$options key=id item=title}
        <li data-value="{$id}" class="choice-item">
            <a role="button" href="#" class="choice-item-button">{$title}</a>
        </li>
        {/foreach}
        {$control}
    </ul>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>