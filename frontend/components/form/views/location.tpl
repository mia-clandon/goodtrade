<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}</div>
    {/if}
    <div id="input-region" class="input-group input-group-region">
        <div class="input">
            <input type="text" placeholder="Ваша страна" size="12" disabled value="Казахстан" name="{$name}[country]" class="input-field">
        </div>
        <div id="input-city" class="input input-geo">
            {$control}
            <input type="text" placeholder="Ваш город" {if $city_value}value="{$city_value}"{/if} name="{$name}[city]" class="input-field">
            <div class="tips">
                <ul class="tips-body"></ul>
            </div>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>