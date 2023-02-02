<?php

namespace common\models\commercial;

use common\models\Base;
use common\models\firms\Firm;
use common\models\Location;
use common\models\Notification;
use frontend\models\commercial\RequestData;

/**
 * Модель для работы с коммерческим запросом.
 * This is the model class for table "commercial_request".
 *
 * @property integer $id
 * @property integer $notification_id
 * @property integer $product_id
 * @property integer $from_firm_id
 * @property integer $to_firm_id
 * @property integer $part_size
 * @property integer $request_validity
 * @property integer $request_time
 * @property integer $status
 * @property integer $city_id
 * @property integer $region_id
 * @property string $address
 * @property string $extra_data
 *
 * @author Артём Широких kowapssupport@gmail.com
 */
class Request extends Base {

    const TABLE_NAME = 'commercial_request';

    const PROP_DATE_TO_UNIXTIME = 1;
    const PROP_DATE_TO = 2;
    const PROP_REQUEST_DATE_UNIXTIME = 3;
    const PROP_REQUEST_DATE = 4;

    /** новый запрос. */
    const STATUS_NEW = 1;
    /** запрос просрочен. */
    const STATUS_OVERDUE = 4;
    /** запрос удалён. */
    const STATUS_DELETED = 5;
    /** запрос отвечен. */
    const STATUS_ANSWERED = 6;

    /** Сроки действия коммерческого запроса. */
    const DAYS_7 = 7;
    const DAYS_14 = 14;
    const DAYS_21 = 21;
    const DAYS_30 = 30;
    const DAYS_60 = 60;

    /**
     * Сроки действия коммерческого запроса.
     * todo: static
     * @return array
     */
    public function getRequestValidity(): array {
        return [
            self::DAYS_7    =>  self::DAYS_7,
            self::DAYS_14   =>  self::DAYS_14,
            self::DAYS_21   =>  self::DAYS_21,
            self::DAYS_30   =>  self::DAYS_30,
            self::DAYS_60   =>  self::DAYS_60,
        ];
    }

    /**
     * Сроки действия коммерческого запроса (строками).
     * @return array
     */
    public static function getRequestValidityNamed(): array {
        return [
            self::DAYS_7    => '7 дней',
            self::DAYS_14   => '14 дней',
            self::DAYS_21   => '21 день',
            self::DAYS_30   => '30 дней',
            self::DAYS_60   => '60 дней',
        ];
    }

    /**
     * Список статусов коммерческого запроса.
     * @return array
     */
    public function getStatuses(): array {
        return [
            self::STATUS_NEW,
            self::STATUS_OVERDUE,
            self::STATUS_DELETED,
        ];
    }

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['product_id', 'from_firm_id', 'to_firm_id', 'part_size', 'request_validity', 'request_time', 'status', 'city_id', 'region_id', 'address'], 'required'],
            [['product_id', 'from_firm_id', 'to_firm_id', 'part_size', 'request_validity', 'request_time', 'status', 'city_id', 'region_id', 'notification_id'], 'integer'],
            [['extra_data'], 'string'],
            [['product_id'], 'unique',
                'targetAttribute' => ['product_id', 'from_firm_id', 'to_firm_id', 'status'], 'message' => 'Подобный коммерческий запрос уже отправлен.'],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * Возвращает данные по датам коммерческого запроса.
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getRequestDates() {
        $date_to_unixtime = strtotime('+'.$this->request_validity.' day', $this->request_time);
        return [
            # До какого числа действует коммерческий запрос. #
            // unixtime вариант.
            self::PROP_DATE_TO_UNIXTIME => $date_to_unixtime,
            // строковый вариант.
            self::PROP_DATE_TO => \Yii::$app->formatter->asDate($date_to_unixtime, 'php:j F Y'),

            // дата коммерческого запроса.
            self::PROP_REQUEST_DATE_UNIXTIME => $this->request_time,
            // строковый вариант.
            self::PROP_REQUEST_DATE => \Yii::$app->formatter->asDate($this->request_time, 'php:j F Y'),
        ];
    }

    /**
     * Возвращает количество оставшихся дней действия коммерческого запроса.
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getLeftDays() {
        $request_date = new \DateTime();
        $request_date->setTimestamp($this->getRequestDates()[self::PROP_DATE_TO_UNIXTIME]);
        $now = new \DateTime();
        return $request_date->diff($now)->format("%a");
    }

    /**
     * Установка данных адреса поставки.
     * @param integer $region_id
     * @param integer $city_id
     * @param string $address
     * @return $this
     */
    public function setLocation($region_id, $city_id, $address) {
        $this->region_id = (int)$region_id;
        $this->city_id = (int)$city_id;
        $this->address = (string)$address;
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification() {
        return $this->hasOne(Notification::class, ['id' => 'notification_id']);
    }

    /**
     * Метод возвращает заполненный объект RequestData.
     * @return $this|null|RequestData
     * @throws \yii\base\Exception
     */
    public function getRequestData() {
        if (!empty($this->extra_data)) {
            return (new RequestData())
                ->setData($this->extra_data);
        }
        return null;
    }

    /**
     * Делает уведомление удаленным.
     * @return bool
     */
    public function setDeletedNotification() {
        /** @var Notification $notification */
        $notification = $this->getNotification()->one();
        if ($notification) {
            return $notification->setDeletedState();
        }
        return false;
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
     * Получение коммерческого запроса на товар от пользователя.
     * @param int $product_id
     * @param int $from_firm_id
     * @return static
     */
    public static function getRequestByProduct($product_id, $from_firm_id) {
        return static::findOne([
            'product_id' => (int)$product_id,
            'from_firm_id' => (int)$from_firm_id,
        ]);
    }

    /**
     * Получение срока действия коммерческого запроса на товар от пользователя.
     * @param int $product_id
     * @param int $from_firm_id
     * @return int|null
     */
    public static function getRequestValidityByProduct($product_id, $from_firm_id) {
        $request = static::findOne([
            'product_id' => (int)$product_id,
            'from_firm_id' => (int)$from_firm_id,
        ]);
        if ($request) {
            return (int)$request->request_validity;
        }
        return null;
    }

    /**
     * Метод проверяет, посылала ли организация коммерческий запрос на товар.
     * @param int $product_id
     * @param int $from_firm_id
     * @return bool
     */
    public static function hasRequest($product_id, $from_firm_id) {
        $request = static::getRequestByProduct($product_id, $from_firm_id);
        if ($request) {
            return true;
        }
        return false;
    }

    /**
     * Метод проверят отправлял ли я коммерческий запрос на товары.
     * @param array $product_ids
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function hasAllCommercialRequest(array $product_ids) {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        return Request::find()
                ->where([
                    'from_firm_id'  => Firm::get()->id,
                    'product_id'    => $product_ids,
                ])
                ->andWhere(['!=', 'status', Request::STATUS_DELETED])
                ->count() == count($product_ids);
    }

    /**
     * Метод возвращает адресс поставки товара.
     * @return string
     */
    public function getAddressString() {
        if ($this->isNewRecord) {
            return '';
        }
        $location = (new Location())
            ->getFormattedLocationText($this->city_id, $this->region_id);
        if ($location) {
            return rtrim($location.', '.$this->address, ', ');
        }
        return '';
    }

    /**
     * Получение размера партии коммерческого запроса на товар от пользователя.
     * @param int $product_id
     * @param int $from_firm_id
     * @return int|null
     */
    public static function getRequestPartSizeByProduct($product_id, $from_firm_id) {
        $request = static::findOne([
            'product_id' => (int)$product_id,
            'from_firm_id' => (int)$from_firm_id,
        ]);
        if ($request) {
            return (int)$request->part_size;
        }
        return null;
    }

    public function attributeLabels() {
        return [
            'id'                => 'ID',
            'notification_id'   => 'Notification ID',
            'product_id'        => 'Product ID',
            'from_firm_id'      => 'From Firm ID',
            'to_firm_id'        => 'To Firm ID',
            'part_size'         => 'Размер партии',
            'request_validity'  => 'Срок действия запроса',
            'request_time'      => 'Время запроса',
            'status'            => 'Статус',
            'city_id'           => 'City ID',
            'region_id'         => 'Region ID',
            'address'           => 'Адрес',
            'extra_data'        => 'Extra Data',
        ];
    }
}