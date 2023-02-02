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

<div class="modal__title">Коммерческий запрос</div>

<div class="modal__notice modal__notice_success"<?= !$show_send_form ? ' style="display:block;"' : '' ?>>
    <span class="modal__notice-title">Поздравляем!</span>
    <div class="modal__notice-text">Ваш коммерческий запрос отправлен. Ответ должен поступить менее, чем через
        <span data-type="request-validity"><?= $product->getCommercialRequestValidity() ?? 0; ?></span> дней.</div>
</div>

<? if ($show_send_form) { ?>
    <?= $form; ?>
<? } ?>