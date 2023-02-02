<?php

namespace frontend\forms\commercial;

use common\models\goods\Product;
use yii\helpers\ArrayHelper;

use common\models\commercial\Request as CommercialRequestModel;
use common\models\firms\Firm;

use frontend\components\lib\notification\extra_object\CommercialRequest;
use frontend\components\lib\notification\Notification;
use frontend\components\form\controls\Region;

/**
 * todo: удалить класс.
 * Class Request
 * Форма отправки коммерческого запроса.
 * @package frontend\forms\product
 * @author yerganat
 */
class RequestAll extends Request {

    /** @var array  */
    private $product_ids = [];

    /**
     * Отправка коммерческого запроса.
     * TODO: сделать транзакцию. нет запроса - нет уведомления и наоборот.
     * @return bool
     */
    public function save(): bool {
        $result = true;
        $request_data = $this->getControlsData();

        /** @var CommercialRequestModel $model */
        $model = $this->getModel();
        $model->setAttributes($request_data);

        // ошибка отправки коммерческого запроса.
        if (!$model->validate(['address', 'region', 'part_size'])) {
            $errors = $model->getErrors();
            $this->populateErrorsFromAR($errors);
            return false;
        }

        $part_size = (int)ArrayHelper::getValue($request_data, 'part_size');
        $request_validity = (int)ArrayHelper::getValue($request_data, 'request_validity');

        $product_id_param = (string)ArrayHelper::getValue($request_data, 'product_id');

        $this->product_ids = explode(',', $product_id_param);

        foreach ($this->product_ids as $product_id) {
            /** @var Product $product */
            $product = Product::findOne(['id' => $product_id]);

            if(!$product) {
                continue;
            }

            if(CommercialRequestModel::hasRequest($product_id, Firm::get()->id)) {
                continue;
            }

            /** @var CommercialRequestModel $model */
            $model = new CommercialRequestModel();

            $model->part_size = $part_size;
            $model->request_validity =  $request_validity;

            // данные адреса поставки.
            $city_id = (int)ArrayHelper::getValue($request_data, 'region.' . Region::KEY_CITY_ID);
            $region_id = (int)ArrayHelper::getValue($request_data, 'region.' . Region::KEY_REGION_ID);
            $address = (string)ArrayHelper::getValue($request_data, 'address');

            $model->setLocation($region_id, $city_id, $address);

            // информация об организациях.
            $model->from_firm_id = Firm::get()->id;
            $model->to_firm_id =  (int)$product->firm_id;
            $model->product_id =  $product->id;

            $model->request_time = time();
            $model->status = CommercialRequestModel::STATUS_NEW;

            $result = $model->save();


            if ($result) {
                // отправка уведомления пользователю.
                $notification_id = Notification::i()
                    ->setType(Notification::NOTIFICATION_TYPE_COMMERCIAL_REQUEST)
                    ->setFromFirmId(Firm::get()->id)
                    ->setToFirmId($model->to_firm_id)
                    ->setExtraDataObject(
                    // дополнительные данные уведомления.
                        (new CommercialRequest())
                            ->setProductId($model->product_id)
                            ->setRequestId($model->id)
                    )
                    ->createNotification();
                if ($notification_id !== false) {
                    $model->notification_id = (int)$notification_id;
                    $model->save(false, ['notification_id']);
                }
            }
        }
        return boolval($result);
    }
}