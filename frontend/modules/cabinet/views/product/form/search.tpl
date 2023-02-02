{$form_start}
<div class="search">
<div class="input-group input-group-search">
    <div class="input">
        {$query}
        <div class="block-inline"><span>Найдено:</span>
            <div class="tumbler">
                <a data-tab="product-list" data-count="{$product_found_count}" href="#" role="button"
                   class="tumbler-button tumbler-button-active"><span>{$product_found_count_stringify}</span></a>
                <a data-tab="price-list" data-count="{$price_list_found_count}" href="#" role="button" class="tumbler-button"><span>{$price_list_found_count_stringify}</span></a>
            </div>
        </div>
    </div>
</div>
{$submit}
    <div class="row">
        <div class="col-xs-6">
            <div class="search-panel">
                {if $has_active_filters}
                    <a href="{$clear_filter_sort_form}" class="search-panel__link">&larr; Вернуться к общему списку</a>
                {/if}
                <div class="sort">
                    <div class="sort-text">Сортировать:</div>
                    <ul class="sort-list">
                    {foreach key=sort item=link from=$sort_links}
                        <li class="{if $link.is_active}is-active{/if} sort-list-item-{if $link.current_direction === 'desc'}down{else}up{/if}">
                            <a href="{$link.url}">{$sort}</a>
                        </li>
                    {/foreach}
                    </ul>
                </div>
                <div class="search-filters">
                    <div class="search-filters__filter">
                        <div class="search-filters__title">Категория:</div>
                        <div class="search-filters__link-container dropdown">
                            {if ($filter_category_id === null)}
                                <a href="#" class="search-filters__link dropdown-toggle">
                                    Все категории
                                </a>
                            {else}
                                <ul class="search-filters__list">
                                    <li class="search-filters__list-item">
                                        <span>{$filter_category_name}</span>
                                        <a href="{$filter_category_drop_url}" class="search-filters__list-item-remove"></a>
                                    </li>
                                </ul>
                            {/if}
                            <div class="dropdown-menu dropdown-menu_search-filters">
                                <div class="dropdown-menu-head">
                                    <div class="dropdown-menu-title">Категории</div>
                                </div>
                                <div class="dropdown-menu-body">
                                    <ul class="linear-list">
                                        {foreach key=url item=category from=$all_my_product_categories}
                                        <li><a href="{$url}">{$category}</a></li>
                                        {/foreach}
                                        {*<li><a href="#">Животноводство</a>
                                            <ul>
                                                <li><a href="#">Промышленные печи</a></li>
                                            </ul>
                                        </li>*}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{*параметры поиска*}
{foreach key=name item=value from=$search_params}
    <input type="hidden" name="{$name}" value="{$value}" />
{/foreach}
{$form_end}