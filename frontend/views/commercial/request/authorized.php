<?

use common\models\goods\Product;

/**
 * Форма с коммерческим запросом для авторизованного пользователя.
 * @var string $form
 * @var Product $product
 * @author Артём Широких kowapssupport@gmail.com
 */

// отправлял ли я уже коммерческий запрос ?
$has_mine_commercial_request = $product->hasMineCommercialRequest();
$show_send_form  = !$has_mine_commercial_request;

?>

<? if ($show_send_form) { ?>
    <div class="popup-head">
        <div class="popup-title">Коммерческий запрос</div>
    </div>
<? } ?>

<div class="popup-body">
    <?
        if ($show_send_form) {
            echo $form;
        }
    ?>
    <div class="popup-message commercial-request-success <?=$show_send_form?'':'is-visible';?>">
        <div class="popup-message-title">Поздравляем!</div>
        <div class="popup-message-text">Ваш коммерческий запрос отправлен.<br>Ответ должен поступить менее чем через
            <span class="request-validity"><?= $product->getCommercialRequestValidity() ?? 0;?></span> дней.
        </div>
    </div>
</div>

<? if ($show_send_form) { ?>
    <div class="popup-foot">
        <button id="popup-send" class="btn btn-blue">Отправить запрос</button>
        <button id="popup-cancel" class="btn btn-link">Отменить</button>
    </div>
<? } ?>