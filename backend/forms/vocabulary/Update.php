<?php

namespace backend\forms\vocabulary;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\controls\Select;
use backend\components\form\Form;

use common\models\Vocabulary;

use yii\helpers\Url;

/**
 * Class Update
 * @package backend\forms\vocabulary
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form  {

    protected function initControls(): void {
        parent::initControls();
        $this->registerJsScript();

        $title = (new Input())
            ->setName('title')
            ->setPlaceholder('Введите название характеристики')
        ;
        $this->registerControl($title);

        // тип характеристики.
        $type_control = (new Select())
            ->setName('type')
            ->setTitle('Выберите тип характеристики')
            ->setArrayOfOptions((new Vocabulary())->getTypes())
        ;
        if (!$this->getModel()->isNewRecord) {
            $type_control->setDisabled();
        }
        $this->registerControl($type_control);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);

        if (!$this->getModel()->isNewRecord) {
            $add_more = (new Button())
                ->setName('add_more')
                ->setContent('Добавить еще')
                ->setType(Button::TYPE_BUTTON)
                ->setButtonType(Button::BTN_TYPE_INFO)
                ->setRedirectOnClick(Url::to(['/vocabulary/update']))
            ;
            $this->registerControl($add_more);
        }
        else {
            $this->addTemplateVars(['add_more' => '']);
        }
    }

    public function validate(): array {
        /** @var Vocabulary $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    protected function populateModel(): void {
        /** @var Vocabulary $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        foreach ($form_data as $attribute_name => $value) {
            if ($model->hasAttribute($attribute_name)) {
                $model->{$attribute_name} = $value;
            }
        }
    }

    public function save(): bool {
        return $this->getModel()->save();
    }
}