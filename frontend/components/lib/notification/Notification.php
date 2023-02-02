<?

namespace frontend\components\lib\notification;

use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\Url;

use common\libs\traits\Singleton;
use common\models\firms\Firm;

use frontend\components\lib\notification\extra_object\CommercialRequest;
use frontend\components\lib\notification\interfaces\IExtraDataInterface;
use frontend\components\lib\notification\traits\Placeholders;

/**
 * Class Notification
 * Управление уведомлениями пользователя.
 * @package frontend\components\lib\notification
 * @author Артём Широких kowapssupport@gmail.com
 */
class Notification {
    use Singleton;
    use Placeholders;

    #region Типы уведомлений.
    /** Тип уведомления - коммерческий запрос. */
    const NOTIFICATION_TYPE_COMMERCIAL_REQUEST  = 1;
    /** Тип уведомления - обратный звонок. */
    const NOTIFICATION_TYPE_CALLBACK            = 2;
    /** Тип уведомления - коммерческое предложение. */
    const NOTIFICATION_TYPE_COMMERCIAL_RESPONSE = 3;
    #endregion

    /** Уведомление от оператора системы. */
    const NOTIFICATION_FROM_PROJECT = 0;

    #region плейсхолдеры для текстов уведомления.

    // строковое название организации.
    const PLACEHOLDER_FIRM_NAME         = '{firm_name}';
    // идентификатор организации автора уведомления.
    const PLACEHOLDER_FROM_FIRM_ID      = '{from_firm_id}';
    // идентификатор организации получателя уведомления.
    const PLACEHOLDER_TO_FIRM_ID        = '{to_firm_id}';
    // ссылка на организацию отправителя.
    const PLACEHOLDER_FROM_FIRM_LINK    = '{from_firm_link}';

    #endregion

    /** @var null|integer - отправитель уведомления. */
    private $from_firm_id = self::NOTIFICATION_FROM_PROJECT;
    /** @var null|integer - получатель уведомления. */
    private $to_firm_id = null;
    /** @var null|integer - тип уведомления. */
    private $type = null;
    /** @var null|string - заголовок уведомления. */
    private $title = null;
    /** @var null|string - текст уведомления. */
    private $text = null;
    /** @var null|IExtraDataInterface */
    private $extra_data = null;

    protected function getPlaceholders() {
        return [
            self::PLACEHOLDER_FIRM_NAME,
            self::PLACEHOLDER_FROM_FIRM_ID,
            self::PLACEHOLDER_TO_FIRM_ID,
            self::PLACEHOLDER_FROM_FIRM_LINK,
        ];
    }

    /**
     * Создание объекта данных по типу уведомления.
     * @return IExtraDataInterface|null
     */
    private function getExtraDataObject() {
        // объект установлен.
        if (!is_null($this->extra_data)) {
            return $this->extra_data;
        }
        // получение объекта по типу.
        if ($this->type == self::NOTIFICATION_TYPE_COMMERCIAL_REQUEST) {
            return new CommercialRequest();
        }
        return null;
    }

    /**
     * Список уведомлений организации.
     * @return ActiveQuery
     */
    public function getFirmNotifications() {
        return \common\models\Notification::find()
            ->where(['to_firm_id' => Firm::get()->id, 'deleted' => 0])
            ->orderBy('send_time DESC')
        ;
    }

    /**
     * Создание уведомления.
     * @return int (идентификатор созданного уведомления)|false
     */
    public function createNotification() {

        $extra_data = $this->getExtraDataObject();

        $notification_title = $this->getTitle();
        if (is_null($notification_title) && $extra_data) {
            // беру заголовок уведомления с доп.данных.
            $notification_title = $extra_data->getTitle();
        }

        $notification_text = $this->getText();
        if (is_null($notification_text) && $extra_data) {
            // беру текст уведомления с доп.данных.
            $notification_text = $this->replacePlaceholders($extra_data->getText());
        }

        /** @var \common\models\Notification $notification */
        $notification = new \common\models\Notification();
        $notification->setAttributes([
                'from_firm_id'  => $this->getFromFirmId(),
                'to_firm_id'    => $this->getToFirmId(),
                'type'          => $this->getType(),
                'title'         => $notification_title,
                'text'          => $notification_text,
            ]);
        ;
        if (!is_null($this->extra_data)) {
            $notification->extra_data = $this->extra_data->getData();
        }
        $notification->send_time = time();
        return ($notification->save()) ? $notification->id : false;
    }

    /**
     * Метод устанавливает объект с дополнительными данными.
     * @param IExtraDataInterface $object
     * @return $this
     * @throws Exception
     */
    public function setExtraDataObject(IExtraDataInterface $object) {
        if ($object->getRelationNotificationType() !== $this->type) {
            throw new Exception('Тип дополнительных данных уведомления не соответствует типу уведомления.');
        }
        $this->extra_data = $object;
        return $this;
    }

    /**
     * @return IExtraDataInterface|null
     */
    public function getExtraData() {
        return $this->extra_data;
    }

    protected function replacePlaceholdersData($placeholder, $content) {
        #region - Замена плейсхолдера на контент.

        // название организации.
        if ($placeholder == self::PLACEHOLDER_FIRM_NAME) {
            $content = $this->replacePlaceholder(
                $placeholder, $this->getFirmName(), $content
            );
        }
        // идентификаторы организаций.
        if ($placeholder == self::PLACEHOLDER_FROM_FIRM_ID) {
            $content = $this->replacePlaceholder(
                $placeholder, $this->getFromFirmId(), $content
            );
        }
        if ($placeholder == self::PLACEHOLDER_TO_FIRM_ID) {
            $content = $this->replacePlaceholder(
                $placeholder, $this->getToFirmId(), $content
            );
        }
        // ссылка на организацию "отправитель".
        if ($placeholder == self::PLACEHOLDER_FROM_FIRM_LINK) {
            $content = $this->replacePlaceholder(
                $placeholder, $this->getFromFirmLink(), $content
            );
        }

        #endregion
        return $content;
    }

    /**
     * Получение ссылки на страницу организации отправителя.
     * @return string
     */
    private function getFromFirmLink() {
        return Html::a($this->getFirmName(), Url::to(['firm/show', 'id' => $this->getFromFirmId()]));
    }

    /**
     * Метод возвращает название организации отправителя по id.
     * @return string
     */
    private function getFirmName() {
        $firm_id = $this->getFromFirmId();
        if (is_null($firm_id)) {
            return '';
        }
        /** @var Firm $firm */
        $firm = Firm::findOne($firm_id);
        if (!$firm) {
            return '';
        }
        return (string)$firm->title;
    }

    /**
     * @return int|null
     */
    public function getFromFirmId() {
        return $this->from_firm_id;
    }

    /**
     * @param int|null $from_firm_id
     * @return $this
     */
    public function setFromFirmId($from_firm_id) {
        $this->from_firm_id = $from_firm_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getToFirmId() {
        return $this->to_firm_id;
    }

    /**
     * @param int|null $to_firm_id
     * @return $this
     */
    public function setToFirmId($to_firm_id) {
        $this->to_firm_id = $to_firm_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param int|null $type
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param null|string $title
     * @return $this
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param null|string $text
     * @return $this
     */
    public function setText($text) {
        $this->text = $text;
        return $this;
    }
}