{$form_start}

<ul class="nav nav-tabs">
    <li class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Основное</a></li>
    <li class="ml-4"><a href="#vocabulary" aria-controls="vocabulary" role="tab" data-toggle="tab">Характеристики
            товара</a></li>
    <li class="ml-4"><a href="#places" aria-controls="places" role="tab" data-toggle="tab">Место реализации</a></li>
</ul>

{$product_id}

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">

        <div class="panel panel-default">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Основная информация</h6></div>
            <div class="card-body">
                {$title}
                {$category_id}
                {$from_import}
                {$status}
                {$mark_type}
                {$images}
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Информация о владельце</div>
            <div class="panel-body">
                {$firm_id}
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Информация о цене</div>
            <div class="panel-body">
                {$price}
                {$unit}
                {$price_with_vat}
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Мощности</div>
            <div class="panel-body">
                {$capacities_from}
                {$capacities_to}
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">О товаре</div>
            <div class="panel-body">
                {$delivery_terms}
                {$text}
            </div>
        </div>
    </div>

    <!-- характеристики -->
    <div role="tabpanel" class="tab-pane" id="vocabulary">
        <br/>
        <div class="vocabulary-wrapper" style="width: 600px"></div>
        <br/>
    </div>

    <!-- место реализации -->
    <div role="tabpanel" class="tab-pane" id="places">
        <br/>
        <div class="places-wrapper" style="width: 600px"></div>
        <br/>
    </div>
</div>

{$submit}

{$form_end}