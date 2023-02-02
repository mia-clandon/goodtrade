<div class="panel panel-default">
    <div class="panel-heading">
        {$form_title}
    </div>
    <div class="panel-body">
        <p>Подсказки, забитые в текстовые поля - отображают пример настроек прайс листа (Агроэксперт.)</p>
        {$form_start}
        <div class="row">
            <div class="col-md-6">
                {$col_count_in_row}
                {$header_col_index}
            </div>
            <div class="col-md-6">
                {$title_col_index}
                {$price_col_index}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {$id} {$firm_id} {$submit}
            </div>
        </div>
        {$form_end}
    </div>
</div>