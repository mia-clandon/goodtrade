{$form_start}

<div class="card m-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Основная информация
        </h6>
    </div>
    <div class="card-body">
        {$title}
        {$status}
        {$user_id}
        {* $profile_id *}
        {$categories}
        {$is_top}
    </div>
</div>

<div class="card m-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            Фото организации
        </h6>
    </div>
    <div class="card-body">
        {$image}
    </div>
</div>

<div class="card m-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            Описание организации
        </h6>
    </div>
    <div class="card-body">
        {$text}
    </div>
</div>

<div class="card m-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            Данные о местоположении
        </h6>
    </div>
    <div class="card-body">
        {$country_id}
        {$region_id}
        {$city_id}
        {$legal_address}
    </div>
</div>

<div class="card m-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            Реквизиты организации
        </h6>
    </div>
    <div class="card-body">
        {$bin}
        {$bank}
        {$bik}
        {$iik}
        {$kbe}
        {$knp}
    </div>
</div>

<div class="ml-4 m-4">
    {$submit}
</div>

{$form_end}