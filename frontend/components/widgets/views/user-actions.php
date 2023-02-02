<?
/**
 * Модальное окно с формами входа/восстановления пароля.
 * @var string $sign_form;
 * @var string $reset_password_form;
 * @author Артём Широких kowapssupport@gmail.com
 */
?>
<div id="modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-logo">
            <img src="/img/modal-logo.png" alt="logotip">
        </div>
        <div class="modal">
            <a role="button" href="#" class="modal-close">
                <i class="icon icon-close"></i>
            </a>
            <div class="modal-body">
                <div class="ui-switcher">
                    <?= $sign_form; // форма входа.?>
                    <?= $reset_password_form; // форма восстановления пароля.?>
                </div>
            </div>
        </div>
    </div>
</div>