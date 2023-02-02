<div class="form-control bin-control" {if $is_hidden}style="display: none;"{/if}>
    {if $label}
    <label class="form-control-label">{$label}</label>
    {/if}
    <div id="input-company" class="input has-tips">
        {$control}
        <div class="tips">
            <div class="tips-body"></div>
            <div class="tips-foot">
                <a role="button" href="#" data-action="skip" class="tips-not-found">Я не нашел свою компанию</a>
            </div>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>