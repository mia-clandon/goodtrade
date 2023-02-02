<?php

namespace frontend\modules\cabinet\forms\company;

use common\models\firms\Firm;
use frontend\components\form\controls\Email;
use frontend\components\form\controls\Input;
use frontend\components\form\controls\Location;
use frontend\components\form\controls\Phone;
use frontend\forms\site\Join;
use yii\helpers\ArrayHelper;

/**
 * Class Settings
 * @package frontend\modules\cabinet\forms\company
 * @author Артём Широких kowapssupport@gmail.com
 */
class Settings extends Join
{

    protected function initControls(): void
    {

        $this->addClass('cabinet-company-form');
        $this->registerFirstStepControls();

        $submit = (new Input())
            ->setType(Input::TYPE_SUBMIT)
            ->setName('submit')
            ->setDisplayNone();
        $this->registerControl($submit);
    }

    public function setModel(mixed $model): static
    {
        /** @var Firm $model */
        parent::setModel($model);

        //из за разных имен контролов модели и формы, приходится устанавливать руками все первоначальные значения.

        // название и бин компании.
        $this->getCompanyTitleControl()->setValue($model->title);
        $this->getCompanyBinControl()->setValue($model->bin);

        // информация о местоположении.
        $location = $this->getLocationControl();
        $location->setValue([
            Location::KEY_CITY_ID => $model->city_id,
            Location::KEY_REGION_ID => $model->region_id,
        ]);

        // юридический адресс организации.
        $legal_address = $this->getLegalAddress();
        $legal_address->setValue($model->legal_address);

        // телефоны организации.
        $phones = $this->getPhoneControl();
        $this->populatePhones($phones);

        // почтовые адреса.
        $emails = $this->getEmailControl();
        $this->populateEmails($emails);

        // фото организации.
        $this->getImageControl()->setTitle("Логотип")->setValue($model->image);

        // сферы деятельности организации.
        $this->getActivityControl()->setValue($model->getCategoryIds());

        // пополнение значений реквизитов.
        $this->getBankControl()->setValue($model->bank);
        $this->getBikControl()->setValue($model->bik);
        $this->getIikControl()->setValue($model->iik);
        $this->getKbeControl()->setValue($model->kbe);
        $this->getKnpControl()->setValue($model->knp);
        $this->getCompanyDescriptionControl()->setValue($model->text);

        return $this;
    }

    /**
     * Заполняет Email control данными с модели.
     * @param Email $control
     */
    protected function populateEmails(Email $control)
    {
        /** @var Firm $model */
        $model = $this->getModel();
        /** @var \common\models\firms\Email[] $emails */
        $emails = $model->getEmails()->all();
        $data = [];
        foreach ($emails as $record) {
            $data[] = $record->email;
        }
        $control->setValue($data);
    }

    /**
     * Заполняет Phone control данными с модели.
     * @param Phone $control
     */
    protected function populatePhones(Phone $control)
    {
        /** @var Firm $model */
        $model = $this->getModel();
        /** @var \common\models\firms\Phone[] $phones */
        $phones = $model->getPhones()->all();
        $data = [];
        foreach ($phones as $record) {
            $data[] = $record->phone;
        }
        $control->setValue($data);
    }

    /**
     * @return array
     */
    public function validate(): array
    {
        /** @var Firm $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();

        //todo: добавить фильтрацию text поля.

        //из за разных имен контролов модели и формы, приходится устанавливать правильные значения.
        $errors = $model->getErrors();
        $new_errors = [];
        foreach ($errors as $property => $error) {
            $new_errors[\frontend\models\form\Join::COMPANY_DATA_PREFIX . $property]
                = $error;
        }
        $this->populateErrorsFromAR($new_errors);
        return parent::validate();
    }

    protected function populateModel(): void
    {
        $data = $this->getControlsData();
        //из за разных имен контролов модели и формы, приходится устанавливать правильные значения.
        $new_data = [];
        foreach ($data as $property_name => $property_value) {
            if (strpos($property_name, \frontend\models\form\Join::COMPANY_DATA_PREFIX) == 0) {
                $new_data[str_replace(\frontend\models\form\Join::COMPANY_DATA_PREFIX, '', $property_name)]
                    = $property_value;
            } else {
                $new_data[$property_name] = $property_value;
            }
        }
        /** @var Firm $model */
        $model = $this->getModel();
        $model->setAttributes($new_data);
        $model
            ->setRegionId(ArrayHelper::getValue($new_data, 'location.' . Location::KEY_REGION_ID))
            ->setCityId(ArrayHelper::getValue($new_data, 'location.' . Location::KEY_CITY_ID))
            ->setCategories(ArrayHelper::getValue($new_data, 'activity', []))
            ->setPhones(ArrayHelper::getValue($new_data, 'phone', []))
            ->setEmails(ArrayHelper::getValue($new_data, 'email', []))
            ->setImageForUpload(ArrayHelper::getValue($new_data, 'image'));
    }

    public function save(): bool
    {
        $this->getModel()->save();
        return true;
    }
}