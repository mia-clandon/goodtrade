{$form_start}
{$category_id}
{*Блок поиска ОКЭД'ов*}
    <table class="table">
        <tr>
            <td>{$name}</td>
            <td>{$from_oked}</td>
            <td>{$to_oked}</td>
            <td>{$find_oked}</td>
        </tr>
    </table>
{*Блок просмотра результатов поиска*}
<div class="found-oked-list-wrapper" style="
            padding-bottom: 10px;
            max-height: 300px;
            overflow: scroll;
            display: none;
            overflow-x: hidden;">
    <table>
        <tbody>
            <tr class="oked-template" style="display: none">
                <td>
                    <div class="checkbox" style="margin-bottom: 0;">
                        <label>
                            <input type="checkbox" name="oked[]" value="::value"><strong>::value</strong> ::name
                        </label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{*Блок уже привязанных окэд'ов к категории*}
<div class="related-oked-list">
    <p>Список привязанных к категории ОКЭД'ов:</p>
    <table>
        <tbody>
        {foreach from=$oked_list item=oked}
            <tr>
                <td>
                    <div class="checkbox" style="margin-bottom: 0;">
                        <label>
                            <input checked="checked" type="checkbox" name="oked[]" value="{$oked.key}">
                                <strong>{$oked.key}</strong> {$oked.name}
                        </label>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

{$form_end}