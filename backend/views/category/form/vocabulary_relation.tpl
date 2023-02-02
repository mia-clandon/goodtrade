{$form_start}

<div class="bs-callout bs-callout-warning" id="callout-tables-responsive-overflow">
    <h4>{$form_title}</h4>
    <p>Выберите характеристику которая будет привязана к категории.</p>
</div>

    {$vocabulary_id}
    {$unit_code}
    {$category_id}

<div class="panel panel-default range-settings" style="display: none">
    <div class="panel-heading">Настройка диапазона значений</div>
    <div class="panel-body">
        {$range_from}
        {$range_to}
        {$range_step}
    </div>
</div>

<div class="panel panel-default select-settings" style="display: none">
    <div class="panel-heading">Возможные значения</div>
    <div class="panel-body">
        {$options}
    </div>
</div>

    {$submit}

{$form_end}