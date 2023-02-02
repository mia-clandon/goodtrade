<?php

namespace frontend\forms\commercial;

use common\libs\form\components\Input as BaseInput;
use common\libs\RegionHelper;
use common\models\firms\Firm;
use common\models\goods\Product;
use frontend\components\form\controls\b2b\Checkbox;
use frontend\components\form\controls\b2b\Input;
use frontend\components\form\controls\b2b\RegionInput;
use frontend\components\form\controls\b2b\Select;
use frontend\components\form\controls\b2b\SliderInput;
use common\models\commercial\Request as CommercialRequestModel;

/**
 * Class RequestNew
 * @property int $part_size
 * @property int $request_validity
 * @package frontend\forms\commercial
 * @author Артём Широких kowapssupport@gmail.com
 */
class RequestNew extends Base {

    /** @var Product */
    private $product;

    /**
     * @throws \yii\base\Exception
     */
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

        $new_type_control = (new BaseInput())
            ->setName('new_type')
            ->setValue('1');
        $this->registerControl($new_type_control);

        // Размер партии.
        $part_size_control = (new SliderInput())
            ->setName('part_size')
            ->setTitle('Размер партии')
            ->setMinValue(1)
            ->setUnitName(($this->product) ? $this->product->getUnitText() : '');
        $this->registerControl($part_size_control);

        // Срок действия запроса.
        $request_validity_control = (new Select())
            ->setName('request_validity')
            ->setTitle('Срок действия запроса')
            ->setArrayOfOptions(CommercialRequestModel::getRequestValidityNamed())
            ->addRule(['required']);
        $this->registerControl($request_validity_control);

        // Регион.
        $region_control = (new RegionInput())
            ->setName('region')
            ->setTitle('Регион')
            ->addRule(['required'])
            ->addRule(['string', 'max' => 255])
            ->addDataAttribute('type', 'company-region')
            ->setValue(RegionHelper::i()
                ->setCountryId(Firm::get()->country_id)
                ->setRegionId(Firm::get()->region_id)
                ->get()
            );
        $this->registerControl($region_control);

        // Адрес.
        $address_control = (new Input())
            ->setName('address')
            ->setTitle('Адрес')
            ->setInputType(Input::GEO_TYPE)
            ->addRule(['required'])
            ->addRule(['string', 'max' => 255])
            ->addDataAttribute('type', 'company-address')
            ->setValue(Firm::get()->legal_address);
        $this->registerControl($address_control);

        // Использовать адрес компании в качестве адреса поставки
        $use_main_address = (new Checkbox())
            ->setName('use_mine_address')
            ->setTitle('Использовать адрес компании в качестве адреса поставки')
            ->addDataAttribute('type', 'location');
        $this->registerControl($use_main_address);
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product): self {
        $this->product = $product;
        return $this;
    }

    public function validate(): array {
        if (!$this->request_validity) {
            $this->addError('request_validity', 'Необходимо выбрать срок действия запроса.');
        }
        return parent::validate();
    }

    public function save(): bool {

        $request_data = $this->getControlsData();
        /** @var CommercialRequestModel $model */
        $model = $this->getModel();
        $model->setAttributes($request_data);

        //todo: доделать сохранение...

        return false;
    }
}