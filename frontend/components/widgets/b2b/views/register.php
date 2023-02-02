<?
/**
 * @var string $form
 * @author yerganat
 */
?>
<div class="overlay" data-type="modal" data-modal="overlay-register">
    <div class="container container_main">
        <div class="row">
            <div class="col-lg-4 m-auto">
                <div class="overlay__modal-container">
                    <div class="overlay__modal-logo-container">
                        <div class="logo logo_md-white"></div>
                    </div>
                    <div class="modal modal_into-overlay">
                        <div class="modal__heading">Зарегистрируйтесь и получите бесплатный премиум доступ на 30 дней</div>
                        <div class="row">
                            <div class="col-lg-4 m-auto">
                                <?= $form; // форма регистрации. ?>
                            </div>
                        </div>
                        <div class="modal__switch-link">
                            <span data-type="modal-open" data-modal="overlay-sign-up">Авторизация</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>