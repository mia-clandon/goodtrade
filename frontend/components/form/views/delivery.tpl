<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}</div>
    {/if}
    <div id="delivery-condition" class="delivery-condition">
        {$control}
        <ul class="delivery-condition-list">
            {foreach from=$delivery_terms key=delivery_term_id item=delivery_term_title}
                <li {if $delivery_term_id|in_array:$selected_delivery_terms}class="is-selected"{/if} data-value="{$delivery_term_id}">{$delivery_term_title}</li>
            {/foreach}
        </ul>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>