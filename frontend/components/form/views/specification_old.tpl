<div class="form-control">
    {if $label}
        <div class="form-control-label">{$label}</div>
    {/if}
    <div id="input-hash" class="input input-hash">
        <input type="text" placeholder="Новый пункт" class="input-hash-field"/>
        <a class="input-hash-button">
            <i class="icon icon-plus"></i>
        </a>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>