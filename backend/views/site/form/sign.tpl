<div class="col-lg-5">
    <div class="card shadow-lg border-0 rounded-lg mt-5">
        <div class="card-header">
            <h3 class="text-center text-gray-900 my-4">Авторизация</h3>
        </div>
        <div class="card-body">
            {$form_start}
            <div class="form-group input-group-lg">
                {$username}
                {if !empty($username_error_string)}
                <div class="alert alert-danger mt-2" role="alert">{$username_error_string}</div>
                {/if}
            </div>
            <div class="form-group input-group-lg">
                {$password}
                {if !empty($password_error_string)}
                    <div class="alert alert-danger mt-2" role="alert">{$password_error_string}</div>
                {/if}
            </div>
            <div class="form-floating mb-3">
                {$submit}
            </div>
            {$form_end}
        </div>
    </div>
</div>
