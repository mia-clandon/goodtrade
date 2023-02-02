<?

namespace frontend\components\lib\notification\extra_object;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\StringHelper;

use frontend\components\lib\notification\interfaces\IExtraDataInterface;
use frontend\components\lib\notification\Notification;

/**
 * Class CommercialResponse
 * Дополнительные данные уведомления с типом "Коммерческое предложение".
 * @package frontend\components\lib\notification\extra_object
 * @author Артём Широких kowapssupport@gmail.com
 */
class CommercialResponse implements IExtraDataInterface {

    const KEY_PRODUCT_ID    = 'product_id';
    const KEY_REQUEST_ID    = 'request_id';
    const KEY_RESPONSE_ID   = 'response_id';

    /** @var null|integer */
    private $product_id = null;
    /** @var null|integer */
    private $request_id = null;
    /** @var null|integer */
    private $response_id = null;

    public function getTitle() {
        return 'Коммерческое предложение';
    }

    public function getText() {
        return '"{from_firm_link}" прислал вам коммерческое предложение...';
    }

    public function getRelationNotificationType() {
        return Notification::NOTIFICATION_TYPE_COMMERCIAL_RESPONSE;
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

    /**
     * @param integer $response_id
     * @return $this
     */
    public function setResponseId($response_id) {
        $this->response_id = intval($response_id);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getResponseId() {
        return (int)$this->response_id;
    }

    public function getData() {
        return Json::encode([
            self::KEY_PRODUCT_ID    => $this->getProductId(),
            self::KEY_REQUEST_ID    => $this->getRequestId(),
            self::KEY_RESPONSE_ID   => $this->getResponseId(),
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
        $response_id = ArrayHelper::getValue($data, self::KEY_RESPONSE_ID);
        if (!is_null($response_id)) {
            $this->setResponseId($response_id);
        }
        return $this;
    }
}