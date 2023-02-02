<?php

namespace backend\forms\import;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\controls\SearchFirm;
use backend\components\form\Form;

use common\libs\form\components\Option;
use common\libs\form\components\Select;
use common\models\firms\Firm;
use common\models\PriceQueue;

use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class Add
 * @package backend\forms\import
 * @author Артём Широких kowapssupport@gmail.com
 */
class Add extends Form {

      public function initControls(): void {

        $firm_component = (new SearchFirm())
            ->setName('firm_id')
            ->setTitle('БИН организации владельца')
            ->setPlaceholder('Введите БИН организации')
        ;
        $this->populateFirm($firm_component);
        $this->registerControl($firm_component);

        $file_control = (new Input())
            ->setTitle('Выберите файл')
            ->setType(Input::TYPE_FILE)
            ->setName('price_file')
        ;
        $this->registerControl($file_control);

        $button = (new Button())
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
            ->setName('submit')
            ->setContent('Загрузить прайс лист')
            ->setType(Button::TYPE_SUBMIT);
        $this->registerControl($button);

    }

    /**
     * @param Select $select
     * @return Select
     */
    private function populateFirm(Select $select) {
        /** @var PriceQueue $model */
        $model = $this->getModel();
        $firm_id = $model->firm_id;
        if ($firm_id) {
            /** @var Firm $firm */
            $firm = Firm::findOne($firm_id);
            if ($firm) {
                $select->addOption(new Option($firm->id, $firm->title));
            }
        }
        return $select;
    }

    /**
     * Заполняет модель данными из формы.
     */
    protected function populateModel(): void {
        /** @var PriceQueue $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();
        foreach ($form_data as $attribute_name => $value) {
            if ($model->hasAttribute($attribute_name)) {
                $model->setAttribute($attribute_name, $value);
            }
        }
        $model->price_file = ArrayHelper::getValue(
            UploadedFile::getInstancesByName('price_file'), 0, null
        );
        $this->setModel($model);
    }

    /**
     * Валидация формы + валидация модели.
     * @return array
     */
    public function validate(): array {
        $this->populateModel();
        $this->getModel()->validate();
        $this->populateErrorsFromAR($this->getModel()->getErrors());
        return parent::validate();
    }

    public function save(): bool {
        /** @var PriceQueue $model */
        $model = $this->getModel();
        $model->upload();
        return $model->save();
    }
}