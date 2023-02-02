<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}
        <span class="form-control-label-tip">Цену указывать не обязательно</span>
    </div>
    {/if}
    <div class="input-group input-group-price">
        <div class="input input-price">
            {$price_control}
            <span class="input-unit input-unit-right">ТГ.</span>
        </div>
        <div id="input-select-unit" class="select has-units">
            <span class="select-unit select-unit-left">За</span>
            {$units_control}
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>