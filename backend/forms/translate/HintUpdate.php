<?php

namespace backend\forms\translate;

use backend\components\form\controls\Button;
use backend\components\form\controls\Select;
use backend\components\form\controls\TextArea;
use backend\components\form\Form;
use common\libs\form\validators\client\Required;
use common\libs\i18n\helper\SystemLanguages;
use common\libs\i18n\models\Hint;

/**
 * Class HintUpdate
 * Обновление текста хинта.
 * @package backend\forms\translate
 * @author Артём Широких kowapssupport@gmail.com
 */
class HintUpdate extends Form {

    public function initControls(): void {
        parent::initControls();
        // категория.
        $category_control = (new Select())
            ->setName('category')
            ->setArrayOfOptions(SystemLanguages::i()->getCategoryNames());
        $this->registerControl($category_control);

        // текст хинта.
        $hint_text_control = (new TextArea())
            ->setName('message')
            ->setJsValidator([
                new Required()
            ]);
        $this->registerControl($hint_text_control);

        $button_control = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY);
        $this->registerControl($button_control);
    }

    public function validate(): array {
        /** @var Hint $model */
        $model = $this->getModel();
        $model->user_id = \Yii::$app->getUser()->id;
        $model->setAttributes($this->getFormData());
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    public function save(): bool {
        /** @var Hint $model */
        $model = $this->getModel();
        return $model->save();
    }
}