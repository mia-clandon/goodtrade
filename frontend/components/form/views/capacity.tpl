<div class="form-control">
    {if $label}
        <div class="form-control-label">{$label}</div>
    {/if}
    <div id="input-range" class="input-group">
        <div class="input has-units"><span class="input-unit input-unit-left">от</span>
            <label>{$from_control}</label><span class="input-unit input-unit-right">штук</span>
        </div>
        <div class="input has-units"><span class="input-unit input-unit-left">до</span>
            <label>{$to_control}</label><span class="input-unit input-unit-right">штук</span>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>