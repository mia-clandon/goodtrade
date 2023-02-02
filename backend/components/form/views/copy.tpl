<div class="form-group {$classes_string}" {$attributes_string}>
    {if $label}
        {$label}
    {/if}
    <table>
        {foreach from=$controls item=control name=controls}
        <tr>
            <td class="copy-control_control">
                <div class="copy-control_control-wrapper">
                    {$control}
                </div>
            </td>
            <td class="copy-control_actions">
                <div class="copy-control_actions-wrapper">

                    {if !$smarty.foreach.controls.first}
                        <button type="button" class="btn btn-default btn-remove-clone" aria-label="Удалить элемент.">
                            <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                        </button>
                    {/if}

                    {if $smarty.foreach.controls.first}
                    <button type="button" class="btn btn-default btn-clone" aria-label="Добавить еще.">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button>
                    {/if}

                </div>
            </td>
        </tr>
        {/foreach}
    </table>
</div>