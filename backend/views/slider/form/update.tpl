<div class="panel panel-default slide-type">
    <div class="panel-body">
        {$form_start}
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label">Тип</label><br/>
                    {foreach from=$tags key=k item=v}
                        <label class="control-label radio-inline">
                            <input type="radio" name="tag" value="{$k}"  data-validation="required"
                                    {if $k == $tag}  checked {/if}
                                    {if $k == 'news' || $k == 'video'}  disabled {/if} >
                            {$v}
                        </label>
                    {/foreach}
                </div>

                {$slide_id}
                {$firm_id}
                {$title}
                {$description}
                {$image}
                {$button}
                {$link}
                {$tip}
            </div>

            <div class="col-lg-6 right">
                <div class="form-group">
                    <label class="control-label">Размер</label><br/>
                    {foreach from=$types key=k item=v}
                        <label class="control-label radio-inline">
                            <input type="radio" name="type" value="{$k}" data-validation="required"
                                    {if $k == $type}  checked {/if}
                                    {if in_array($k, $not_allowed_types) }  disabled {/if}>
                            {$v}
                        </label>
                    {/foreach}
                    <span class="help-block">{$type_error_string}</span>
                </div>
                <div class="form-group">
                    <img class="example one-one" src="/img/slide/1x1.png">
                    <img class="example one-two"  src="/img/slide/1x2.png">
                    <img class="example two-one"  src="/img/slide/2x1.png">
                    <img class="example two-two"  src="/img/slide/2x2.png">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {$submit}
                {$back}
            </div>
        </div>
        {$form_end}
    </div>
</div>