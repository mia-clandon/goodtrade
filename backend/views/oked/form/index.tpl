<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            {$form_title}
        </h6>
    </div>
    <div class="card-body">
        {$form_start}
        <div class="row">
            <div class="col-md-6">
                {$from}
                {$to}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {$submit} {$reset} {$update_sphinx}
            </div>
        </div>
        {$form_end}
    </div>
</div>