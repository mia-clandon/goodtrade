<?php

namespace frontend\forms\site;

use common\models\SoonEmail;
use common\libs\form\components\Button;
use common\libs\form\components\Input;
use common\libs\form\Form;

use common\libs\form\validators\client\Email as EmailClient;
use common\libs\form\validators\client\Required as RequiredClient;

/**
 * Class SoonForm
 * @package frontend\forms\site
 * @author Артём Широких kowapssupport@gmail.com
 * @property $email string
 */
class SoonForm extends Form {

    protected function initControls(): void {

        $input = (new Input())
            ->setType(Input::TYPE_TEXT)
            ->setName('email')
            ->addAttribute('placeholder', 'Введите ваш электронный адрес')
            ->setJsValidator([
                (new EmailClient())->setErrorMessage('Введите правильно e-mail адрес'),
                (new RequiredClient()),
            ])
        ;
        $this->registerControl($input);

        $button = (new Button())
            ->setName('submit')
            ->setType(Button::TYPE_SUBMIT)
            ->setContent('Подписаться')
        ;
        $this->registerControl($button);
    }

    protected function populateModel(): void {
        /** @var SoonEmail $model */
        $model = $this->getModel();
        $model->setAttributes($this->getFormData());
        $model->created = time();
    }

    public function validate(): array {
        $this->populateModel();
        $this->getModel()->validate();
        $this->populateErrorsFromAR($this->getModel()->getErrors());
        return parent::validate();
    }

    /**
     * Сохранение.
     * @return bool
     */
    public function save(): bool {
        return $this->getModel()->save();
    }
}