<div class="form-control">
    {if $label}
        <div class="form-control-label">{$label}</div>
    {/if}
    <ul class="multiply-tech-specs multiply">
        <li class="row-item">
            <div class="input-group input-group-tech-specs">
                <div class="input input-tech-specs">
                    <input type="text" autocomplete="off" name="specification-name[]" placeholder="Новый пункт характеристик">
                    <div class="form-control-message"></div>
                </div>
                <div class="input input-tech-specs">
                    <div class="ui-tags">
                        {* место для вставляемых тегов. *}
                        <div class="tags-container"></div>
                    </div>
                    <input type="text" autocomplete="off" name="tech-specs[]" placeholder="Значения">
                </div>
            </div>
            <a href="#" role="button" data-action="add"></a>
        </li>
    </ul>
    {$control}
    {* Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message common"></div>
</div>