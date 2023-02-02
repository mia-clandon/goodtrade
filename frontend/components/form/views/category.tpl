<div class="form-control">
    {if $label}
        <div class="form-control-label">{$label}</div>
    {/if}
    <div id="product-category" class="pick">
        {$control}
        <a data-action="open" class="pick-add">Выберите сферу деятельности</a>
        <ul class="pick-items">
            <li>
                <a data-action="open" role="button" href="#" class="pick-plus">
                    <i class="icon icon-plus"></i>
                </a>
            </li>
        </ul>
        <div class="tips">
            <div class="tips-body">
                <div class="product-category">
                    <div class="product-category-controls">
                        <ul class="product-category-tabs"></ul><a role="button" href="#" data-action="all" class="product-category-all">Все категории</a>
                    </div>
                    <div class="product-category-content"></div>
                </div>
            </div>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>