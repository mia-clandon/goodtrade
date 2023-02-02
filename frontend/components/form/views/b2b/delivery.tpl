<div class="form-control">
    {if $title}
    <div class="form-control__top-text">
        <div class="form-control__label">{$title}</div>
    </div>
    {/if}
    <div class="checkbox-list row">
        {foreach from=$all_delivery_terms key=id item=term}
        <div class="col-4">
            <div class="checkbox">
                <input name="{$name}[{$id}]" {if in_array($id, $value)}checked="checked"{/if} type="checkbox" class="checkbox__input">
                <label class="checkbox__label">
                    <span class="checkbox__check-mark"></span>{$term}
                </label>
            </div>
        </div>
        {/foreach}
    </div>
    <div class="form-control__bottom-text"></div>
</div>