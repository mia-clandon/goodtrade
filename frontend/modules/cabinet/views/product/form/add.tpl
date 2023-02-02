{* Шаблон формы добавления товара из кабинета. *}
{$form_start}
    <fieldset>

        {* Категория товара. *}
        {$category}
        {* Фото товара. *}
        {$image}
        {* Ориентировочная цена товара. *}
        {$price}
        {* Мощности. *}
        {$capacity}
        {* Условия доставки товара. *}
        {$delivery_terms}
        {* Описание товара. *}
        {$description}

    </fieldset>

    <fieldset class="vocabulary-wrapper">
        <legend>Характеристики товара</legend>
    </fieldset>

    <div id="bottom-controls">
        <div class="pull-right">
            <button type="submit" class="btn btn-blue add-product">
            {if $is_update_product}
                Редактировать товар
            {else}
                Добавить товар
            {/if}
            </button>
        </div>
    </div>
{$form_end}