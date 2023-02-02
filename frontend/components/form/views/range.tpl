{*
<div class="form-control range-wrapper">
    {if $label}
        <div class="form-control-label">{$label}</div>
    {/if}

    <div class="input-group">
        <div class="input has-units"><span class="input-unit input-unit-left">от</span>
            <label><input type="number" class="range-start" value="{$min}" autocomplete="off"></label>
        </div>
        <div class="input has-units"><span class="input-unit input-unit-left">до</span>
            <label><input type="number" class="range-end" value="{$max}" autocomplete="off"></label>
        </div>
    </div>

    <div class="range-control-container">
        {$hidden_control}
        {$control}
    </div>

    {$is_double_checkbox}
    {if $errors|@count gt 0}
        <span class="help-block">{$errors_string}</span>
    {/if}
</div>
*}
{*
В разметке у скрытого поля input[type="hidden"] можно задавать несколько параметров:
data-min - минимальное значение
data-max - максимальное значение
data-unit - единица измерения, которая будет автоматически подставляться к значению
data-decimals - количество знаков после запятой
*}
<div class="form-control">
    <div class="range range_value">
        <input type="hidden" data-min="5" data-max="15" data-unit="" data-decimals="">
        <div class="input-group">
            <div class="input has-units"><span class="input-unit input-unit-left">от</span>
                <label><input type="number" autocomplete="off"></label>
            </div>
            <div class="input has-units"><span class="input-unit input-unit-left">до</span>
                <label><input type="number" autocomplete="off"></label>
            </div>
        </div>
        <div class="range__track-container">
            <div class="range__track">
                <div class="range__button"></div>
                <div class="range__line"></div>
                <div class="range__button"></div>
            </div>
        </div>
        <div class="range__checkbox">
            <div class="ui-checkbox">
                <input type="checkbox">
                <label class="ui-checkbox-label">В виде диапазона</label>

                <div class="form-control-message form-control-message_chechbox"></div>
            </div>
        </div>
    </div>
</div>
