{$form_start}
    {if $form_title}
    <div class="form-title text-center">{$form_title}</div>
    {/if}

    {$email}
    {$submit}

    {* TODO: пока нет возможности высылать код по СМС.
    <div class="form-forgot text-center">
    <a href="#forgot-phone-form" class="btn btn-sm btn-link btn-lowercase ui-switcher-toggle">Я не помню свой email</a>
    </div> *}
    <div class="form-links text-center">
        <a href="#login-form" class="btn btn-sm btn-link btn-lowercase ui-switcher-toggle">Авторизация</a>
        {*
        <div class="delimeter"></div>
        <a href="#join-form" class="btn btn-sm btn-link btn-lowercase ui-switcher-toggle">Регистрация</a>
        *}
    </div>
{$form_end}