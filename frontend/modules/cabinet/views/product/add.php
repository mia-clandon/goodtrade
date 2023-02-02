<?
/**
 * Добавление товара из кабинета. (одиночный товар).
 * @author Артём Широких kowapssupport@gmail.com
 * @var string $form
 * @var string $action
 */
?>
<div class="container">
    <div class="cabinet">

        <div class="row">
            <div class="col-xs-4 col-xs-offset-1">
                <? if ($action === 'add') { ?>
                <h2 class="sub-title">Добавление товара
                    <small><a href="<?= Yii::$app->urlManager->createUrl('cabinet/product/add-price');?>">Загрузить прайс-лист</a></small>
                </h2>
                <? } ?>
                <? if ($action === 'update') { ?>
                    <h2 class="sub-title">Редактирование товара</h2>
                <? } ?>
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