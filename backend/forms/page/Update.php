<?php

namespace backend\forms\page;

use common\models\Page;
use common\libs\form\validators\client\Required as RequiredClient;

use backend\components\form\controls\Button;
use backend\components\form\controls\TextArea;
use backend\components\form\controls\Input;
use backend\components\form\Form;

/**
 * Class Update
 * @package backend\forms\page
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    public function initControls(): void {
        parent::initControls();

        $title_input = (new Input())
            ->setName('title')
            ->setPlaceholder('Введите название страницы')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($title_input);

        $alias_input = (new Input())
            ->setName('alias')
            ->setPlaceholder('Введите название ЧПУ')
            ->setJsValidator([(new RequiredClient())])
            ->addRule(['required'])
        ;
        $this->registerControl($alias_input);

        $text_control = (new TextArea())
            ->setName('text')
            ->setLoadTinyMCE()
            ->addAttributes(['rows' => 12])
        ;
        $this->registerControl($text_control);

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить страницу')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * @return array
     */
    public function validate(): array {
        /** @var Page $model */
        $model = $this->getModel();
        $this->populateModel();
        $model->validate();
        $this->populateErrorsFromAR($model->getErrors());
        return parent::validate();
    }

    protected function populateModel(): void {

        /** @var Page $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        $model->setAttributes($form_data);
    }

    /**
     * @return bool
     */
    public function save(): bool {
        /** @var Page $model */
        $model = $this->getModel();
        return $model->save();
    }
}