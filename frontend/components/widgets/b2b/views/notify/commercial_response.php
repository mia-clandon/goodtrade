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
<div class="elements-list__item">
    <div class="elements-list__item-image">
        <img src="<?= $from_firm_logo; ?>">
    </div>
    <div class="elements-list__item-content">
        <div class="elements-list__item-title">
            <?= $notification->text; ?>
        </div>
        <div class="elements-list__item-additional-text">
            <?= DaysAgo::i()->get($response->response_time);?>
        </div>
        <div class="elements-list__item-buttons-row">
            <a href="<?=Yii::$app->urlManager->createUrl(['commercial/show-response', 'response' => $response->id,])?>" class="button button_small button_link">
                <span class="button__text">Смотреть подробнее</span>
            </a>
        </div>
    </div>
</div>