<div class="form-control">
    {if $label}
    <div class="form-control-label">{$label}
        <span class="form-control-label-tip">{$label_tip}</span>
    </div>
    {/if}
    <div class="ui-category{if (!empty($classes))} {$classes}{/if}"
         data-is-product-category-state='{$is_product_category_state}'
         data-firm-activities-ids='{$firm_activities_ids_json}'
         data-selected-values='{$selected_id_json}'>
        <div class="ui-tags">
            {* шаблон вставляемого тега. *}
            <div class="ui-tag-template">
                <span class="ui-tags-item" style="display: none">
                    <i class="icon{* класс иконки. *}"></i>
                    {* название тега. *}
                    <span class="ui-tags-item-label"></span>
                    <input type="hidden" name="{$name}">
                    <a role="button" href="#" class="ui-tags-item-close">
                        <i class="icon icon-close"></i>
                    </a>
                </span>
            </div>
            {* место для вставляемых тегов. *}
            <div class="tags-container">
                {* Вывод установленных категорий. *}
                {foreach from=$selected_categories item=category name=categories}
                    <span class="ui-tags-item">
                        <i class="icon {$category.icon_class}"></i>
                        <span class="ui-tags-item-label">{$category.title}</span>
                        <input type="hidden" name="{$name}" value="{$category.id}">
                        <a role="button" href="#" class="ui-tags-item-close">
                            <i class="icon icon-close"></i>
                        </a>
                    </span>
                {/foreach}
                {* Выбор сферы деятельности организации. *}
                <a role="button" href="#" class="ui-category-button">
                    <span class="ui-category-button-hidden">Выберите категорию</span>
                    <span class="ui-category-button-text">Выберите сферу деятельности</span>
                </a>
            </div>
        </div>

        {* Выбор категории товара. *}
        <div class="ui-product-menu-wrap" style="display: none;">
            <!--<a role="button" href="#" class="ui-product-menu-back">Назад</a>-->
            <ul class="ui-product-menu">
                {$selected_activities_html}
                {* место для подгрузки категорий. *}
                <li><a role="button" href="#" class="ui-product-menu-back all">Все категории</a></li>
            </ul>
        </div>

        {* Модальное окно со сферами деятельности. *}
        <div style="display: none" class="ui-category-menu">
            <ul class="ui-category-menu-list activities">
                <li>
                    <a role="button" href="#" class="ui-category-menu-button">
                        <span class="ui-category-menu-button-hidden hide">Скрыть другие категории</span>
                        <span class="ui-category-menu-button-text show">Показать другие категории</span>
                    </a>
                </li>

                {foreach from=$activities item=category name=activities}
                <li
                    {if $smarty.foreach.activities.iteration > 5}
                        style="display: none"
                    {/if}
                    data-value="{$category.id}"
                    data-icon="{$category.icon_class}"
                    class="ui-category-menu-list-item {$category.icon_class} {if in_array($category.id, $selected_id)}ui-category-menu-list-item-active{/if}"
                >{$category.title}</li>
                {/foreach}
            </ul>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>