<div class="form-control">
    {if $label}
    <label for="popup-geo-region" class="form-control-label">{$label}</label>
    {/if}
    <div id="input-city" class="input input-geo">
        {$input_control}
        {$hidden_control}
        <div class="tips">
            <div class="tips-body"></div>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>