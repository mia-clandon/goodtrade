<?

namespace frontend\components\lib\notification\extra_object;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\StringHelper;

use frontend\components\lib\notification\interfaces\IExtraDataInterface;
use frontend\components\lib\notification\Notification;

/**
 * Class CommercialRequest
 * Дополнительные данные уведомления с типом "Коммерческий запрос".
 * @package frontend\components\lib\notification\extra_object
 * @author Артём Широких kowapssupport@gmail.com
 */
class CommercialRequest implements IExtraDataInterface {

    const KEY_PRODUCT_ID = 'product_id';
    const KEY_REQUEST_ID = 'request_id';

    /** @var null|integer */
    private $product_id = null;
    /** @var null|integer */
    private $request_id = null;

    public function getTitle() {
        return 'Коммерческий запрос';
    }

    public function getText() {
        return '"{from_firm_link}" запрашивает коммерческое предложение...';
    }

    public function getRelationNotificationType() {
        return Notification::NOTIFICATION_TYPE_COMMERCIAL_REQUEST;
    }

    /**
     * @param integer $product_id
     * @return $this
     */
    public function setProductId($product_id) {
        $this->product_id = intval($product_id);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getProductId() {
        return (int)$this->product_id;
    }

    /**
     * @param integer $request_id
     * @return $this
     */
    public function setRequestId($request_id) {
        $this->request_id = intval($request_id);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRequestId() {
        return (int)$this->request_id;
    }

    public function getData() {
        return Json::encode([
            self::KEY_PRODUCT_ID => $this->getProductId(),
            self::KEY_REQUEST_ID => $this->getRequestId(),
        ]);
    }

    public function setData($json) {
        if (!StringHelper::isJson($json)) {
            throw new Exception('Передаваемые данные не являются JSON объектом.');
        }
        $data = Json::decode($json);

        //load data
        $product_id = ArrayHelper::getValue($data, self::KEY_PRODUCT_ID);
        if (!is_null($product_id)) {
            $this->setProductId($product_id);
        }
        $request_id = ArrayHelper::getValue($data, self::KEY_REQUEST_ID);
        if (!is_null($request_id)) {
            $this->setRequestId($request_id);
        }
        return $this;
    }
}