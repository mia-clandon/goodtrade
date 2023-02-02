<?php

namespace backend\forms\location;

use backend\components\form\controls\Select;
use backend\components\form\Form;
use backend\components\form\controls\Input;
use backend\components\form\controls\Button;

use common\libs\form\components\Option;
use common\libs\form\validators\client\Required;
use common\libs\traits\RegisterJsScript;
use common\models\Location;

/**
 * Class Update
 * Форма обновления/создания города
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    use RegisterJsScript;

    /**
     * Инициализация компонентов формы.
     * @throws \Exception
     */
    protected function initControls(): void {
        parent::initControls();

        $title_input = (new Input())
            ->setName('title')
            ->setTitle('Название города')
            ->setPlaceholder('Название города')
            ->setJsValidator([(new Required())])
        ;
        $this->registerControl($title_input);

        $region_select = (new Select())
            ->setTitle('Выберите область')
            ->setName('region')
            ->setPlaceholder('Выберите область')
            ->addOption((new Option())->add(0, ''))
        ;
        $location_model = (new Location());
        $regions = $location_model->getPossibleRegions();
        foreach ($regions as $name => $id) {
            $region_select->addOption((new Option($id, $name)));
        }
        $this->registerControl($region_select);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * @return array
     */
    public function validate(): array {
        /** @var Location $model */
        $model = $this->getModel();
        $model->setAttributes($this->getFormData());
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    /**
     * @return bool
     */
    public function save(): bool {
        return $this->getModel()->save();
    }
}