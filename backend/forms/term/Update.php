<?php

namespace backend\forms\term;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\Form;

use common\libs\form\validators\client\Number;
use common\libs\form\validators\client\Required as RequiredClient;
use common\libs\form\validators\client\Length as LengthClient;
use common\models\Vocabulary;
use common\models\VocabularyOption;

/**
 * Class Update
 * @package backend\forms\term
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    /** @var null|Vocabulary */
    private $vocabulary = null;

    protected function initControls(): void {
        parent::initControls();

        $this->setId('term-update-form');

        $value_control = (new Input())
            ->setTitle('Значение для "'. $this->getVocabulary()->title.'"')
            ->setName('value')
            ->setPlaceholder('Введите значение характеристики')
        ;
        $client_validators = [(new RequiredClient())];

        // для текстовых типов.
        if (in_array($this->getVocabulary()->getType(), [Vocabulary::TYPE_TEXT, Vocabulary::TYPE_SELECT])) {
            $client_validators[] = (new LengthClient())->addMax(255);
        }
        // для числовых типов.
        if (in_array($this->getVocabulary()->getType(), [Vocabulary::TYPE_VALUE])) {
            $value_control->setType(Input::TYPE_NUMBER);
            $client_validators[] = (new Number())->allowFloat();
        }

        $value_control->setJsValidator($client_validators);
        $this->registerControl($value_control);

        $vocabulary_id_control = (new Input())
            ->setName('vocabulary_id')
            ->setType(Input::TYPE_HIDDEN)
            ->setValue($this->getVocabulary()->id)
        ;
        $this->registerControl($vocabulary_id_control);

        $button_control = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button_control);
    }

    public function validate(): array {
        /** @var VocabularyOption $model */
        $model = $this->getModel();
        $model->setAttributes($this->getFormData());
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    public function save(): bool {
        return $this->getModel()->save();
    }

    /**
     * @param Vocabulary $vocabulary
     * @return $this
     */
    public function setVocabulary(Vocabulary $vocabulary) {
        $this->vocabulary = $vocabulary;
        return $this;
    }

    /**
     * @return Vocabulary|null
     */
    public function getVocabulary() {
        return $this->vocabulary;
    }
}