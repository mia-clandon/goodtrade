<div class="form-control">
    <div class="form-control__top-text">
        <div class="form-control__label">{$label}</div>
        {if $label_tip}
            <div class="form-control__tip">{$label_tip}</div>
        {/if}
    </div>
    <div class="control-wrapper input">
        {$control}
    </div>
    <div class="form-control__bottom-text"></div>
</div>