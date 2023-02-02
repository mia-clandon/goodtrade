<?php

namespace frontend\forms\commercial;

use yii\helpers\ArrayHelper;

use common\libs\form\components\Input as BaseInput;
use common\models\commercial\Request as CommercialRequestModel;
use common\models\firms\Firm;

use frontend\components\form\controls\Input;
use frontend\components\lib\notification\extra_object\CommercialRequest;
use frontend\components\lib\notification\Notification;
use frontend\components\form\controls\Region;
use frontend\components\form\controls\Select;
use frontend\components\form\controls\Spinner;
use frontend\models\commercial\RequestData;

/**
 * Class Request
 * Форма отправки коммерческого запроса.
 * @package frontend\forms\product
 * @author Артём Широких kowapssupport@gmail.com
 */
class Request extends Base {

    protected function initControls(): void {
        parent::initControls();

        $this->setId('commercial-request-form');

        // id товара.
        $product_id_control = (new BaseInput())
            ->setName('product_id')
            ->setType(Input::TYPE_HIDDEN)
            ->setValue($this->getProductId())
        ;
        $this->registerControl($product_id_control);

        // id организации.
        $firm_id_control = (new BaseInput())
            ->setName('firm_id')
            ->setType(Input::TYPE_HIDDEN)
            ->setValue($this->getFirmId())
        ;
        $this->registerControl($firm_id_control);

        // размер партии.
        $spinner_control = (new Spinner())
            ->setName('part_size')
            ->setTitle('Размер партии')
        ;
        $this->registerControl($spinner_control);

        // срок действия запроса.
        $request_validity_control = (new Select())
            ->setName('request_validity')
            ->setTitle('Срок действия запроса')
            ->setArrayOfOptions((new CommercialRequestModel())->getRequestValidity())
        ;
        $this->registerControl($request_validity_control);

        // регион.
        $region_control = (new Region())
            ->setName('region')
            ->setTitle('Регион')
            //TODO: валидатор
        ;
        $this->registerControl($region_control);

        // адрес.
        $address_control = (new Input())
            ->setName('address')
            ->setTitle('Адрес')
            ->setInputType(Input::GEO_TYPE)
            //TODO: валидатор
        ;
        $this->registerControl($address_control);
    }

    /**
     * Отправка коммерческого запроса.
     * TODO: сделать транзакцию. нет запроса - нет уведомления и наоборот.
     * @return bool
     * @throws \yii\base\Exception
     */
    public function save(): bool {

        $request_data = $this->getControlsData();
        /** @var CommercialRequestModel $model */
        $model = $this->getModel();
        $model->setAttributes($request_data);

        // данные адреса поставки.
        $city_id = (int)ArrayHelper::getValue($request_data, 'region.'.Region::KEY_CITY_ID);
        $region_id = (int)ArrayHelper::getValue($request_data, 'region.'.Region::KEY_REGION_ID);
        $address = (string)ArrayHelper::getValue($request_data, 'address');

        $model->setLocation($region_id, $city_id, $address);

        // данные выбранных характеристик.
        $terms = (array)ArrayHelper::getValue($request_data, 'terms', []);
        $is_all_modifications = (bool)ArrayHelper::getValue($request_data, 'is_all_modifications', false);

        $model->extra_data = (new RequestData())
            ->setTerms($terms)
            ->setIsAllModifications($is_all_modifications)
            ->getData()
        ;

        // информация об организациях.
        $model->from_firm_id = Firm::get()->id;
        $model->to_firm_id = (int)ArrayHelper::getValue($request_data, 'firm_id', 0);

        $model->request_time = time();
        $model->status = CommercialRequestModel::STATUS_NEW;

        $result = $model->save();

        // ошибка отправки коммерческого запроса.
        if (!$result) {
            $errors = $model->getErrors();
            $this->populateErrorsFromAR($errors);
            return false;
        }

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
            return true;
        }
        return boolval($result);
    }
}