<?

namespace frontend\components\widgets\b2b;

/**
 * Class NotifyManager
 * @package frontend\components\widgets\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class NotifyManager extends \frontend\components\widgets\NotifyManager {

    public function run() {
        if (\Yii::$app->user->isGuest) {
            return '';
        }
        $is_landing = \Yii::$app->controller->id === "site"
            && \Yii::$app->controller->action->id === "index";
        return $this->render('notify/list', [
            'notification_list'     => $this->getNotifyList(),
            'notification_count'    => $this->getNotifyQuery()->count(),
            'is_landing'            => $is_landing,
        ]);
    }
}