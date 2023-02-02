<div class="component-upload-{$component_name}">
    <input type="hidden" name="upload-component" value='{$settings}'>
    {if is_array($images) && $images|@count gt 0 }
        <div class="row {$image_sorting_class}">
            {foreach from=$images key=image_id item=image}
                <div class="col-xs-6 col-md-3 image-block">
                    <a class="thumbnail preview-server">
                        <span {foreach from=$additional_params key=attribute item=value}{$attribute}="{$value}"{/foreach} style="position: absolute; top: 6px; cursor: pointer;"
                        data-image-id="{$image_id}" data-remove-action="{$remove_action}" class="glyphicon glyphicon-remove"></span>
                        <img src="{$image}">
                        {if !empty($image_sorting_class)}
                            <input type="hidden" value="{$image_id}" name="images_order[]"/>
                        {/if}
                    </a>
                </div>
            {/foreach}
        </div>
    {/if}
    <div style="display: none;" class="progress-bar-wrap">
        <p>
            <strong>Загрузка</strong>
            <span class="pull-right text-muted">n% Complete</span>
        </p>
        <div class="row previews"></div>
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                <span class="sr-only">n% Complete (success)</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        {if $label}
        <label>{$label}</label>
        {/if}
        <div class="form-control">{$control}</div>
    </div>
</div>