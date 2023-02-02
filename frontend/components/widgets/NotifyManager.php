<?

namespace frontend\components\widgets;

use yii\base\Widget;

use common\models\commercial\Response;
use common\models\commercial\Request;

use frontend\components\lib\notification\extra_object\CommercialRequest as CommercialRequestExtra;
use frontend\components\lib\notification\extra_object\CommercialResponse as CommercialResponseExtra;
use frontend\components\lib\notification\Notification;

/**
 * Class NotifyManager
 * Менеджер уведомлений пользователя.
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class NotifyManager extends Widget {

    public function run() {
        parent::run();
        return $this->render('notify/list', [
            'notification_list'     => $this->getNotifyList(),
            'notification_count'    => $this->getNotifyQuery()->count(),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected function getNotifyQuery() {
        return Notification::i()->getFirmNotifications();
    }

    /**
     * Метод собирает уведомления организации в зависимости от типа.
     * @return string
     */
    protected function getNotifyList() {
        $notifications = [];
        $notification_list = $this->getNotifyQuery()->all();
        /** @var \common\models\Notification $notification */
        foreach ($notification_list as $notification) {

            // коммерческий запрос.
            if ($notification->type == Notification::NOTIFICATION_TYPE_COMMERCIAL_REQUEST) {

                $extra_data = (new CommercialRequestExtra())
                    ->setData($notification->extra_data);

                $request_id = $extra_data->getRequestId();
                /** @var Request $request */
                $request = Request::findOne($request_id);
                if ($request) {
                    $notifications[] = $this->render('notify/commercial_request', [
                        'request'           => $request,
                        'extra_data'        => $extra_data,
                        'notification'      => $notification,
                        'from_firm_logo'    => $notification->getFromFirmLogo(),
                    ]);
                }
            }

            // коммерческое предложение.
            if ($notification->type == Notification::NOTIFICATION_TYPE_COMMERCIAL_RESPONSE) {

                $extra_data = (new CommercialResponseExtra())
                    ->setData($notification->extra_data);

                $response = Response::findOne($extra_data->getResponseId());
                if ($response) {
                    $notifications[] = $this->render('notify/commercial_response', [
                        'response'          => $response,
                        'extra_data'        => $extra_data,
                        'notification'      => $notification,
                        'from_firm_logo'    => $notification->getFromFirmLogo(),
                    ]);
                }
            }
        }
        return implode('', $notifications);
    }
}