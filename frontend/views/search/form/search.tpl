{$form_start}

<div class="input input_no-focus">
    <div class="icon icon_search"></div>

    {*Input поиска.*}
    {$query}

    <div class="icon icon_settings" data-action="filter-form-toggle"></div>

    {if $product_count || $firms_count}
    <div class="tumbler">
        <div class="tumbler__text">Найдено:</div>
        <div class="tumbler__buttons">
            {if $product_count > 0}
            <a href="#" class="tumbler__button tumbler__button_active">{$product_count} товаров</a>
            {/if}
            {if $firms_count > 0}
            <a href="#" class="tumbler__button">{$firms_count} компании</a>
            {/if}
        </div>
    </div>
    {/if}
</div>

<!--{*todo: поиск в конкретной категории.
Судя по макетам, фильтры поиска были переделаны в отдельную форму в правой колонке либо во всплывающем окне. *}
<div class="bar-top__search-params">
    <div class="bar-top__search-params-list">
        <div class="bar-top__search-params-row">
            <div class="bar-top__search-params-title">В категории:</div>
            <div class="bar-top__search-params-items">
                <div class="breadcrumbs">
                    <div class="breadcrumbs__item">
                        <div class="breadcrumbs__item-icon breadcrumbs__item-icon_agriculture"></div>
                    </div>
                    <div class="breadcrumbs__item">
                        <div class="breadcrumbs__item-title">
                            <span>Пшеница мягкая</span>
                        </div>
                        <div class="breadcrumbs__item-remove"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="button button_small button_link">
        <span class="button__text">Расширенный поиск</span>
        <span class="button__icon button__icon_right button__icon_settings"></span>
    </button>
</div>-->

{$form_end}