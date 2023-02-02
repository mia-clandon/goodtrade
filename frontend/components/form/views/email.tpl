<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}</div>
    {/if}
    <ul id="multiply-email" class="multiply">
        {foreach from=$controls key=index item=control}
        <li>
            {if $index==0}
                <div class="input input-mail input-mail-main">
                    {$control->render()}
                    <div class="label">Основной</div>
                    {* Блок для вывода сообщений (например, об ошибках). *}
                    <div class="form-control-message"></div>
                </div>
                <a href="#" role="button" data-action="add"></a>
            {else}
                <div class="input input-mail">
                    {$control->render()}
                    {* Блок для вывода сообщений (например, об ошибках). *}
                    <div class="form-control-message"></div>
                </div>
                <a href="#" role="button" data-action="del"></a>
            {/if}
        </li>
        {/foreach}
    </ul>
    {* Общие для контрола ошибки.*}
    <div class="form-control-message common"></div>
</div>