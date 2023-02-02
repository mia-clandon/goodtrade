<?php

namespace common\models\commercial;

use yii\behaviors\TimestampBehavior;
use yii\db\Exception;

use common\models\goods\Product;
use common\models\firms\Firm;
use common\models\Base;

use frontend\components\lib\notification\extra_object\CommercialResponse;
use frontend\components\lib\notification\Notification;
use frontend\models\commercial\ResponseData;

/**
 * This is the model class for table "commercial_response".
 *
 * @property integer $id
 * @property integer $notification_id
 * @property integer $from_firm_id
 * @property integer $to_firm_id
 * @property integer $request_id
 * @property integer $product_id
 * @property integer $response_validity
 * @property integer $pre_payment
 * @property integer $post_payment
 * @property integer $time_to_send
 * @property integer $in_stock
 * @property integer $product_count
 * @property string $product_price
 * @property string $with_vat
 * @property integer $response_time
 * @property string $extra_data
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Response extends Base {

    /** количество товара. */
    const PROP_PRODUCT_COUNT = 'product_count';
    /** срок действия коммерческого предложения. */
    const PROP_RESPONSE_VALIDITY = 'response_validity';
    /** срок до отправки. */
    const PROP_TIME_TO_SEND = 'time_to_send';
    /** товар в наличии. */
    const PROP_PRODUCT_IN_STOCK = 'in_stock';
    /** цена товара. */
    const PROP_PRODUCT_PRICE = 'product_price';
    /** цена товара. */
    const PROP_PRODUCT_WITH_VAT = 'with_vat';
    /** предоплата. */
    const PROP_PRE_PAYMENT = 'pre_payment';
    /** постоплата. */
    const PROP_POST_PAYMENT = 'post_payment';
    /** условия поставки. */
    const PROP_DELIVERY_CONDITION = 'delivery_condition';
    /** адресс компании. */
    const PROP_COMPANY_ADDRESS = 'company_address';
    /** город организации. */
    const PROP_COMPANY_CITY = 'company_city';
    /** email организации. */
    const PROP_COMPANY_EMAIL = 'company_email';
    /** телефоны организации. */
    const PROP_COMPANY_PHONE = 'company_phone';

    /** бин организации. */
    const PROP_COMPANY_BIN = 'company_bin';
    /** банк организации. */
    const PROP_COMPANY_BANK = 'company_bank';
    /** бик организации. */
    const PROP_COMPANY_BIK = 'company_bik';
    /** иик организации. */
    const PROP_COMPANY_IIK = 'company_iik';
    /** кбе организации. */
    const PROP_COMPANY_KBE = 'company_kbe';
    /** кнп организации. */
    const PROP_COMPANY_KNP = 'company_knp';

    /** новое предложение. */
    const STATUS_NEW = 1;
    /** предложение отправлено. */
    const STATUS_SEND = 2;
    /** предложение просрочено. */
    const STATUS_OVERDUE = 4;
    /** предложение удалено. */
    const STATUS_DELETED = 5;

    /** Сроки действия коммерческого предложения. */
    const DAYS_7 = 7;
    const DAYS_14 = 14;
    const DAYS_21 = 21;
    const DAYS_30 = 30;
    const DAYS_60 = 60;

    /**
     * Статус коммерческого предложения.
     * @param int $status
     * @return $this
     */
    public function setStatus($status) {
        $this->status = (int)$status;
        return $this;
    }

    /**
     * Получение коммерческого ответа на товар пользователью.
     * @param int $product_id
     * @param int $to_firm_id
     * @return static
     */
    public static function getResponseByProduct($product_id, $to_firm_id) {
        return static::findOne([
            'product_id' => (int)$product_id,
            'to_firm_id' => (int)$to_firm_id,
        ]);
    }

    /**
     * Сроки действия коммерческого предложения.
     * @return array
     */
    public function getResponseValidity() {
        return [
            self::DAYS_7    =>  self::DAYS_7,
            self::DAYS_14   =>  self::DAYS_14,
            self::DAYS_21   =>  self::DAYS_21,
            self::DAYS_30   =>  self::DAYS_30,
            self::DAYS_60   =>  self::DAYS_60,
        ];
    }

    public static function tableName() {
        return 'commercial_response';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->response_time = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Отправка коммерческого предложения пользователю.
     * @return bool
     */
    public function sendResponse() {
        $this->setStatus(static::STATUS_SEND);
        if ($this->save()) {
            // изменяю статус коммерческого запроса на "отвечен".
            /** @var Request $request */
            $request = $this->getRequest()->one();
            if ($request) {
                $request->status = Request::STATUS_ANSWERED;
                $result = $request->save();
                if ($result) {
                    // удаляю уведомление о запросе коммерческого запроса.
                    $request->setDeletedNotification();
                    // отправляю уведомление пользователю о коммерческом предложении.
                    return $this->sendNotifyToUser();
                }
            }
        }
        return false;
    }

    /**
     * Отправляет пользователю уведомление о коммерческом предложении.
     * @return bool
     */
    public function sendNotifyToUser() {
        if ($this->isNewRecord) {
            return false;
        }
        $notification_id = Notification::i()
                ->setType(Notification::NOTIFICATION_TYPE_COMMERCIAL_RESPONSE)
                ->setFromFirmId(Firm::get()->id)
                ->setToFirmId($this->to_firm_id)
                ->setExtraDataObject(
                    // дополнительные данные уведомления.
                    (new CommercialResponse())
                        ->setProductId($this->product_id)
                        ->setRequestId($this->request_id)
                        ->setResponseId($this->id)
                )
                ->createNotification()
        ;
        if ($notification_id !== false) {
            $this->notification_id = $notification_id;
            $this->save(false, ['notification_id']);
        }
        return ($notification_id > 0);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest() {
        return $this->hasOne(Request::class, ['id' => 'request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification() {
        return $this->hasOne(\common\models\Notification::class, ['id' => 'notification_id']);
    }

    /**
     * Владелец коммерческого запроса.
     * @return null|Firm
     */
    public function getFirmOwner() {
        if ($this->isNewRecord) {
            return null;
        }
        return Firm::findOne($this->from_firm_id);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct() {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Получатель коммерческого предложения.
     * @param int $to_firm_id
     * @return $this
     */
    public function setToFirmId($to_firm_id) {
        $this->to_firm_id = (int)$to_firm_id;
        return $this;
    }

    /**
     * Владелец коммерческого предложения.
     * @param int $from_firm_id
     * @return $this
     */
    public function setFromFirmId($from_firm_id) {
        $this->from_firm_id = (int)$from_firm_id;
        return $this;
    }

    /**
     * Устанавливает идентификатор комммерческого запроса.
     * @param int $request_id
     * @return $this
     * @throws Exception
     */
    public function setRequestId($request_id) {
        /** @var Request $request */
        $request = Request::findOne($request_id);
        if (!$request) {
            throw new Exception('Request not found.');
        }
        $this->setProductId($request->product_id);
        $this->setToFirmId($request->from_firm_id);
        $this->request_id = (int)$request_id;
        return $this;
    }

    /**
     * Id товара коммерческого предложения.
     * @param int $product_id
     * @return $this
     */
    public function setProductId($product_id) {
        $this->product_id = (int)$product_id;
        return $this;
    }

    /**
     * Цена коммерческого предложения (за единицу товара).
     * @param float $price
     * @return $this
     */
    public function setProductPrice($price) {
        if (is_string($price)) {
            $price = (int)trim(preg_replace('/\s+/', '', $price));
        }
        $this->product_price = (int)$price;
        return $this;
    }

    /**
     * Устанавливает значение (Товар с НДС)
     * @param bool $with_vat
     * @return $this
     */
    public function setWithVat($with_vat = true) {
        $this->with_vat = (int)$with_vat;
        return $this;
    }

    /**
     * Устанавливает процент предоплаты.
     * @param int $percent
     * @return $this
     */
    public function setPrePayment($percent) {
        $this->pre_payment = (int)$percent;
        return $this;
    }

    /**
     * Устанавливает процент постоплаты.
     * @param int $percent
     * @return $this
     */
    public function setPostPayment($percent) {
        $this->post_payment = (int)$percent;
        return $this;
    }

    /**
     * Устанавливает количество дней до отправки товара.
     * @param int $days
     * @return $this
     */
    public function setDaysToSend($days) {
        $this->time_to_send = (int)$days;
        return $this;
    }

    /**
     * Количество дней действия коммерческого запроса.
     * @param int $days
     * @return $this
     */
    public function setResponseValidity($days) {
        $days = (int)$days;
        if (array_key_exists($days, $this->getResponseValidity())) {
            $this->response_validity = (int)$days;
        }
        return $this;
    }

    /**
     * Устанавливает значение (на складе.)
     * @param bool $in_stock
     * @return $this
     */
    public function setInStock($in_stock = true) {
        $this->in_stock = (int)$in_stock;
        return $this;
    }

    /**
     * Количество товара.
     * @param int $product_count
     * @return $this
     */
    public function setProductCount($product_count) {
        $this->product_count = (int)$product_count;
        return $this;
    }

    /**
     * Метод возвращает заполненный объект ResponseData.
     * @return null|ResponseData
     */
    public function getResponseData() {
        if (!empty($this->extra_data)) {
            return (new ResponseData())
                ->setData($this->extra_data);
        }
        return null;
    }

    /**
     * Заполняет данные extra_data из объекта ResponseData.
     * @param ResponseData $response_data
     * @return $this
     */
    public function setResponseObject(ResponseData $response_data) {
        $this->extra_data = $response_data->getData();
        return $this;
    }

    public function rules() {
        return [
            [[
                'from_firm_id', 'to_firm_id', 'request_id', 'product_id',
                'response_validity', 'pre_payment', 'post_payment', 'time_to_send', 'status',
                'in_stock', 'with_vat', 'product_count', 'response_time', 'created_at', 'updated_at', 'notification_id',
                ], 'integer'
            ],
            [['product_price'], 'number'],
            [['extra_data'], 'required'],
            [['extra_data'], 'string'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'notification_id' => '№ уведомления',
            'from_firm_id' => 'Владелец коммерческого предложения',
            'to_firm_id' => 'Получатель коммерческого предложения',
            'request_id' => '№ коммерческого запроса',
            'product_id' => '№ товара',
            'response_validity' => 'Срок действия коммерческого предложения',
            'pre_payment' => 'Предоплата',
            'post_payment' => 'Постоплата',
            'time_to_send' => 'Количество дней до отправки',
            'in_stock' => 'На складе ?',
            'with_vat' => 'Цена с НДС',
            'product_count' => 'Количество товара',
            'product_price' => 'Цена за единицу товара',
            'response_time' => 'Дата отправки коммерческого предложения',
            'extra_data' => 'Дополнительные данные коммерческого предложения',
        ];
    }
}