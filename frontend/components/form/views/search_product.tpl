<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}</div>
    {/if}
    <div id="input-product" class="input">
        {$control}
        {$id_control}
        <div class="tips">
            <div class="tips-body"></div>
            <div class="tips-foot">
                <a role="button" href="#" data-action="skip" class="tips-not-found">Я не нашел похожий товар</a>
            </div>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>