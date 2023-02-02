<?
/**
 * @var string $form
 */
    use frontend\components\widgets\SideBar;
?>

<?=SideBar::widget();?>

<main role="main" id="page-wrap" class="no-overflow has-bottom-control has-progress-bar">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-sm-offset-1 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-1">
                <!--                <div id="preloader" class="preloader-overlay preloader-overlay-solid">-->
                <!--                    <div class="preloader">Подождите ...</div>-->
                <!--                </div>-->
                <?= $form; ?>
            </div>
        </div>
    </div>
</main>