{$form_start}

<div class="input input_inline input_sm input_no-borders">
    <div class="icon icon_search"></div>
    {*Input поиска.*}
    {$query}
</div>

{*todo: поиск в конкретной категории.
Судя по макетам, фильтры поиска были переделаны в отдельную форму в правой колонке либо во всплывающем окне. *}
<!--<div class="bar-top__search-params">
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
</div>
-->

{$form_end}