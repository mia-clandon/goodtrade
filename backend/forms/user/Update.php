<?php

namespace backend\forms\user;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\Form;


use common\libs\form\validators\client\Required as ClientRequired;
use common\libs\form\validators\client\Email as ClientEmail;
use common\models\User;

/**
 * Class Update
 * @package backend\forms\user
 * @property string phone
 * @property string email
 * @property string password
 * @property string re_password
 * @author Артём Широких kowapssupport@gmail.com
 */
class Update extends Form {

    protected function initControls(): void {
        parent::initControls();

        $phone_control = (new Input())
            ->setName('phone_real')
            ->addAttribute('required', 'required')
            ->setPlaceholder('Основной номер телефона')
            ->setJsValidator([new ClientRequired(),])
            ->addRule(['required'])
        ;
        $this->registerControl($phone_control);

        $email_control = (new Input())
            ->setName('email')
            ->addAttribute('required', 'required')
            ->setPlaceholder('Email пользователя')
            ->setJsValidator([new ClientRequired(), new ClientEmail(),])
            ->addRule(['required'])
            ->addRule(['email'])
        ;
        $this->registerControl($email_control);

        if ($this->getModel()->isNewRecord) {

            $password_control = (new Input())
                ->setName('password')
                ->setType(Input::TYPE_PASSWORD)
                ->addAttribute('required', 'required')
                ->setPlaceholder('Введите пароль, от 3 до 20 символов.')
                ->setJsValidator([new ClientRequired(),])
                ->addRule(['required'])
                ->addRule(['string', 'min' => 3, 'max' => 20])
            ;

            $re_password_control = (new Input())
                ->setName('re_password')
                ->addAttribute('required', 'required')
                ->setJsValidator([new ClientRequired(),])
                ->setPlaceholder('Повторите пароль, от 3 до 20 символов.')
                ->addRule(['required'])
                ->addRule(['string', 'min' => 3, 'max' => 20])
                ->addRule(['compare', 'compareValue' => $this->password, 'message' => 'Пароли не совпадают'])
                ->setType(Input::TYPE_PASSWORD)
                ->setTitle('Повторите пароль')
            ;

            $this->registerControl($password_control);
            $this->registerControl($re_password_control);
        }
        else {

            $password_control = (new Input())
                ->setName('password')
                ->setType(Input::TYPE_PASSWORD)
                ->setPlaceholder('Новый пароль, от 3 до 20 символов.')
            ;
            $this->registerControl($password_control);
        }

        $button = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY)
        ;
        $this->registerControl($button);
    }

    /**
     * Заполняет модель данными из формы.
     */
    protected function populateModel(): void {

        /** @var User $model */
        $model = $this->getModel();
        $form_data = $this->getFormData();

        // имя пользователя = email.
        $model->username = $this->email;

        foreach ($form_data as $attribute => $value) {

            if ($attribute == 'password') {

                if ($model->isNewRecord) {

                    $model->setPassword($value);
                }
                else {
                    // обновление пароля только если его ввели.
                    if (!empty($value)) {
                        $model->setPassword($value);
                    }
                }
            }
            else if ($attribute == 'phone_real') {
                $model->setPhone($value);
            }
            else if ($model->hasAttribute($attribute)) {
                $model->{$attribute} = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function validate(): array {

        $this->populateModel();
        $this->getModel()->validate();
        $this->populateErrorsFromAR($this->getModel()->getErrors());

        if ($phone_errors = $this->getErrors('phone')) {
            foreach ($phone_errors as $error) {
                $this->addError('phone_real', $error);
            }
        }

        return parent::validate();
    }

    /**
     * Сохранение пользователя.
     */
    public function save(): bool {
        return $this->getModel()->save();
    }
}