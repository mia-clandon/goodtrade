<?
/**
 * Модальное окно с формами входа/восстановления пароля.
 * @var string $sign_form;
 * @author yerganat
 */
?>
<div class="overlay" data-type="modal" data-modal="overlay-sign-up">
    <div class="container container_main">
        <div class="row">
            <div class="col-lg-4 m-auto">
                <div class="overlay__modal-container">
                    <div class="overlay__modal-logo-container">
                        <div class="logo logo_md-white"></div>
                    </div>
                    <div class="modal modal_into-overlay">
                        <div class="modal__heading">Авторизация</div>
                        <div class="row">
                            <div class="col-lg-4 m-auto">
                                <?= $sign_form; // форма входа.?>
                            </div>
                        </div>
                        <div class="modal__switch-link">
                            <span data-type="modal-open" data-modal="overlay-register">Регистрация</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>