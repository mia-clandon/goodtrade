<?
/**
 * Уведомление с типом "Коммерческий запрос".
 * @var \common\models\commercial\Request $request
 * @var \frontend\components\lib\notification\extra_object\CommercialRequest $extra_data
 * @var \common\models\Notification $notification
 * @var string $from_firm_logo
 */
use common\libs\DaysAgo;
?>
<div class="notify-company">
    <div class="notify-company-logo">
        <img src="<?= $from_firm_logo; ?>"/>
    </div>
    <div class="notify-company-content">
        <div class="notify-company-date"><?= DaysAgo::i()->get($request->request_time);?></div>
        <div class="notify-company-message"><?= $notification->text; ?></div>
        <a href="<?=Yii::$app->urlManager->createUrl(['commercial/response', 'request' => $request->id,])?>" class="notify-company-btn btn btn-blue">
            Ответить на запрос
        </a>
        <? /*
        <a class="notify-company-more">Подробнее...</a> */ ?>
    </div>
</div>