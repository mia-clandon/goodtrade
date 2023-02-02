<?
/**
 * Уведомление с типом "Коммерческое предложение".
 * @var \common\models\commercial\Response $response
 * @var \frontend\components\lib\notification\extra_object\CommercialResponse $extra_data
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
        <div class="notify-company-date"><?= DaysAgo::i()->get($response->response_time);?></div>
        <div class="notify-company-message"><?= $notification->text; ?></div>
        <a href="<?=Yii::$app->urlManager->createUrl(['commercial/show-response', 'response' => $response->id,])?>" class="notify-company-btn btn btn-blue">
            Подробнее
        </a>
    </div>
</div>