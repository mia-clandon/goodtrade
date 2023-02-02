<?

namespace frontend\components\lib\notification\extra_object;

use common\libs\StringHelper;
use frontend\components\lib\notification\interfaces\IExtraDataInterface;
use frontend\components\lib\notification\Notification;
use frontend\components\lib\notification\traits\Placeholders;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class CommercialRequest
 * Дополнительные данные уведомления с типом "Заказать обратный звонок".
 * @package frontend\components\lib\notification\extra_object
 * @author Артём Широких kowapssupport@gmail.com
 */
class Callback implements IExtraDataInterface {
    use Placeholders;

    const KEY_USER_NAME     = 'user_name';
    const KEY_USER_PHONE    = 'user_phone';

    #region - плейсхолдеры
    const PLACEHOLDER_USER_NAME     = '{user}';
    const PLACEHOLDER_USER_PHONE    = '{phone}';
    #endregion;

    /** @var null|string */
    private $user_name;
    /** @var null|string */
    private $user_phone;

    protected function getPlaceholders() {
        return [
            self::PLACEHOLDER_USER_NAME,
            self::PLACEHOLDER_USER_PHONE,
        ];
    }

    public function getRelationNotificationType() {
        return Notification::NOTIFICATION_TYPE_CALLBACK;
    }

    public function getTitle() {
        return 'Обратный звонок';
    }

    public function getText() {
        return $this->replacePlaceholders('{user} запросил у вас обратный звонок по номеру {phone}');
    }

    protected function replacePlaceholdersData($placeholder, $content) {

        if ($placeholder == self::PLACEHOLDER_USER_NAME) {
            $content = $this->replacePlaceholder($placeholder, $this->getName(), $content);
        }

        if ($placeholder == self::PLACEHOLDER_USER_PHONE) {
            $content = $this->replacePlaceholder($placeholder, $this->getPhone(), $content);
        }

        return $content;
    }

    /**
     * @param string $user_name
     * @return $this
     */
    public function setName($user_name) {
        $this->user_name = (string)$user_name;
        return $this;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone) {
        $this->user_phone = (string)$phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return (string)$this->user_name;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return (string)$this->user_phone;
    }

    public function setData($json) {
        if (!StringHelper::isJson($json)) {
            throw new Exception('Передаваемые данные не являются JSON объектом.');
        }
        $data = Json::decode($json);

        //load data
        $user_name = ArrayHelper::getValue($data, self::KEY_USER_NAME);
        if (!is_null($user_name)) {
            $this->setName($user_name);
        }
        $user_phone = ArrayHelper::getValue($data, self::KEY_USER_PHONE);
        if (!is_null($user_phone)) {
            $this->setPhone($user_phone);
        }
        return $this;
    }

    public function getData() {
        return Json::encode([
            self::KEY_USER_NAME     => $this->getName(),
            self::KEY_USER_PHONE    => $this->getPhone(),
        ]);
    }
}