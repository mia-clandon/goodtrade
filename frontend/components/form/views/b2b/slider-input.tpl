<div class="form-control">
    <div class="form-control__top-text">
        {if $label}
        <div class="form-control__label">{$label}</div>
        {/if}
    </div>
    <div class="range range_value">

        {* Параметры для контрола. *}
        <input type="hidden" data-max="{$max_value}"/>
        <input type="hidden" data-min="{$min_value}"/>
        <input type="hidden" data-decimals="{$decimals}"/>
        <input type="hidden" data-unit="{$unit_name}"/>

        <div class="input-group">
            <div class="input">
                {$control}
                <div class="input__text">{$unit_name}</div>
            </div>
        </div>

        <div class="range__track-container">
            <div class="range__track">
                <div class="range__button"></div>
                <div class="range__line"></div>
                <div class="range__button"></div>
            </div>
        </div>

        <div class="form-control__bottom-text"></div>
    </div>
</div>