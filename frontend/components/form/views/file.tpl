<div class="form-control block">
    <div class="input-file-uploader" {foreach from=$options key=option item=value}data-{$option}="{$value}" {/foreach}>
        {if $label}
        <div class="form-control-label">{$label}</div>
        {/if}
        <div id="input-file-photo" class="input-file">
            {$file_control}
            <div class="input-hidden-wrap"></div>
            <div class="files"></div>
            {* Начальное состояние загрузчика *}
            <div class="input-file-placeholder">
                <div class="input-file-text">Перетащите сюда файл или<br>
                    <a role="button" data-action="add"> выберите его с вашего компьютера</a>
                </div>
            </div>
            {* Отображение процесса загрузки файла. *}
            <div class="input-file-thumbnails-wrap" style="display:none;">
                <div class="input-file-text">Загружается файл «<span></span>»</div>
                <div class="input-file-progress">
                    <div class="input-file-progress-bar">
                        <span data-progress="47" class="progress-bar"></span>
                        <i class="progress-bar-stop">Stop</i>
                    </div>
                </div>
            </div>
            {* Отображение "файл загружен". *}
            <div class="input-file-thumbnails-wrap success" style="display:none;">
                <div class="input-file-text">Файл «<span></span>» загружен.</div>
            </div>
        </div>
    </div>
    {* Добавлено frontend-чуваком. Блок для вывода сообщений (например, об ошибках). *}
    <div class="form-control-message"></div>
</div>