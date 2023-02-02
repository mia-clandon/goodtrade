<?
/**
 * Добавление товара из кабинета. (загрузка прайс листов.)
 * @author Артём Широких kowapssupport@gmail.com
 * @var string $form
 */
?>
<div class="container">
    <div class="cabinet">
        <div class="row">
            <div class="col-xs-4 col-xs-offset-1">
                <h2 class="sub-title">Загрузка прайс-листа
                    <small><a href="<?= Yii::$app->urlManager->createUrl(['cabinet/product/add']);?>">Добавить один товар</a></small>
                </h2>
            </div>
        </div>
        <div class="cabinet-form">
            <div class="row">
                <div class="col-xs-6 col-sm-4 col-sm-offset-1 col-md-4 col-md-offset-1 col-lg-4 col-lg-offset-1">
                    <?= $form; ?>
                </div>
            </div>
        </div>
    </div>
</div>