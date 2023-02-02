<div class="form-control">
    {if $label}
        <div class="form-control-label">{$label}</div>
    {/if}
    <div id="{$control_id}" class="input-photo">
        {$control}
        <div class="input-hidden-wrap"></div>
        <div class="input-photo-placeholder" {if $has_images}style="display: none;"{/if}>
            <div class="input-photo-text">Перетащите сюда фотографии или<br>
                <a role="button" href="#" data-action="add"> выберите их с вашего компьютера</a>
            </div>
        </div>
        <div class="input-photo-thumbnails-wrap" {if $has_images}style="display: block;"{/if}>
            <div class="input-photo-drop-text">Перетащите сюда фото,<br>которое хотите сделать<br>главным</div>
            <ul class="input-photo-thumbnails input-photo-thumbnails_sortable">
                {foreach from=$images item=image}
                    <li data-action="select" data-type="from-server">
                        <img src="{$image.thumbnail}" alt="{$image.thumbnail}" data-image-original="{$image.original}">
                        <a href="#" role="button" class="input-photo-del" data-action="del"></a>
                        <a></a>
                    </li>
                {/foreach}
            </ul>
            {if $is_multiple}
            <a role="button" href="#" data-action="add" class="input-photo-btn">Добавить фото</a>
            {/if}
        </div>
    </div>
    {* Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>