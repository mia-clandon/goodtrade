<?
/**
 * Вывод избарнных компании и товаров
 * @var \common\models\firms\Firm[] $firms
 * @var \common\models\goods\Product $products
 * @var \common\models\firms\Firm $firms
 * @var \common\models\Chrono[] $chronos
 * @var array $firm_product_array
 * @var int $firm_id
 */

use frontend\components\widgets\CommercialRequest;
?>

<?=
\Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/parts/sidebar.php'), [
    'firms' => $firms,
    'current_firm' => $firm,
    'firm_product_array' => $firm_product_array,
    'firm_id' => $firm_id,
]);
?>

<div class="container">
    <?
    if ($firm !== null) {
        echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/firm.php'), [
               'firm' => $firm,
               'products' => $products,
               'chronos' => $chronos,
               'firm_id' => $firm_id
        ]);
    }
    else if (!is_null($firm_id) && $firm_id==0) {
        echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/parts/products.php'), [
               'products' => $products,
        ]);
    }
    else {
        echo Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/favorite/blank.php'), [
        ]);
    }
    ?>

</div>


<? if(!empty($firm_id)) {?>
<div id="bottom-controls" class="has-border">
    <div class="pull-right">
        <button data-id="1" data-vertical-align="bottom" data-horizontal-align="right" class="btn btn-blue popup-toggle callback-popup">
            <span>Заказать обратный звонок</span><i class="icon icon-callme-white"></i>
        </button>
    </div>
</div>
<? } ?>
<!-- for modals -->
<div id="popup-commerce" data-type="callback" class="popup popup-form is-hidden">
    <div class="popup-head">
        <div class="popup-title">Заказать обратный звонок</div>
    </div>
    <div class="popup-body">
        <div class="popup-message popup-message-error">
            <div class="popup-message-text">Для того, чтобы отправить запрос на обратный звонок<br>вам нужно указать ваши имя и телефон.</div>
        </div>
        <form>
            <fieldset>
                <div class="form-control">
                    <div class="form-control-label">Введите ваше имя</div>
                    <div class="input input-name">
                        <input type="email" placeholder="Введите ваше имя" name="name[]" pattern="[a-z0-9._%+-]">
                    </div>
                </div>
                <div class="form-control">
                    <div class="form-control-label">Введите ваш телефон</div>
                    <div class="input input-tel">
                        <input type="tel" placeholder="+7 (777) 707 00 77" name="phone[]">
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="popup-foot">
        <button id="popup-send" class="btn btn-blue">Заказать</button>
        <button id="popup-cancel" class="btn btn-link">Отменить</button>
    </div>
</div>

<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL]); ?>