{$form_start}
<div class="languages">
    <ul class="nav nav-tabs language-tabs">
        {foreach from=$tabs key=language_code item=language_name name=languages}
            <li {if $smarty.foreach.languages.first}class="active"{/if}>
                <a href="{$language_code}">{$language_name}</a>
            </li>
        {/foreach}
    </ul>
    <div class="tab-content">
        {foreach from=$tabs key=language_code item=language_name name=languages}
        <div class="tab-pane language-modal-controls {if $smarty.foreach.languages.first}active{/if}" id="{$language_code}">

            {*все контролы формы в рамках текущего языка.*}
            {$textarea_controls.$language_code}

        </div>
        {/foreach}
        {$hint_id}
        {$submit}
    </div>
</div>
{$form_end}