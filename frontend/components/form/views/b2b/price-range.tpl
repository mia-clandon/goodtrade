<div class="form-control">
    {if $title}
    <div class="form-control__top-text">
        <div class="form-control__label">{$title}</div>
    </div>
    {/if}
    <div class="input-group">
        <div class="input">
            <div class="input__text input__text_small">от</div>
            {$from_input}
        </div>
        <div class="input">
            <div class="input__text input__text_small">до</div>
            {$to_input}
        </div>
    </div>
    <div class="checkbox checkbox_checked">
        {$vat_checkbox}
        <label class="checkbox__label">
            <span class="checkbox__check-mark"></span>Цена с учетом НДС
        </label>
    </div>
    <div class="form-control__bottom-text"></div>
</div>