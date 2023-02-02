<?php

namespace frontend\modules\cabinet\forms\profile;

use yii\helpers\ArrayHelper;

use frontend\components\form\controls\Input;

use common\libs\form\Form;
use common\models\User;

/**
 * Class Settings
 *
 * @property mixed|null new_password
 * @property mixed|null current_password
 * @package frontend\modules\cabinet\forms\profile
 * @author Артём Широких kowapssupport@gmail.com
 */
class Settings extends Form {

    protected function initControls(): void {

        $this->registerJsScript([
            'depends' => 'yii\web\JqueryAsset',
        ]);

        $this->addClass('cabinet-profile-form');

        // текущий пароль.
        $current_password_control = (new Input())
            ->setTitle('Текущий пароль')
            ->setPlaceholder('Введите текущий пароль')
            ->setName('current_password')
            ->setType(Input::TYPE_PASSWORD)
            ->addRule(['string', 'min' => 3, 'max' => 20])
        ;
        $this->registerControl($current_password_control);

        // новый пароль.
        $new_password_control = (new Input())
            ->setTitle('Новый пароль')
            ->setPlaceholder('Введите ваш текущий пароль')
            ->setName('new_password')
            ->addRule(['required'])
            ->addRule(['string', 'min' => 3, 'max' => 20])
            ->setType(Input::TYPE_PASSWORD)
        ;
        $this->registerControl($new_password_control);

        // повторите пароль.
        $re_password_control = (new Input())
            ->setTitle('Подтверждение')
            ->setPlaceholder('Повторите новый пароль')
            ->setName('re_password')
            ->setType(Input::TYPE_PASSWORD)
            ->addRule(['required'])
            ->addRule(['compare', 'message' => 'Пароли не совпадают', 'compareValue' => $this->new_password])
        ;
        $this->registerControl($re_password_control);

        $submit = (new Input())
            ->setType(Input::TYPE_SUBMIT)
            ->setName('submit')
            ->setDisplayNone()
        ;
        $this->registerControl($submit);
    }

    public function validate(): array {

        // валидирую "новый пароль" и "повторите пароль" только если ввели старый.
        if((bool)!$this->current_password) {
            $this->setAttributesNotValidate(['new_password', 're_password']);
        }
        // валидирую "повторите пароль" только если введён "новый пароль"
        if((bool)!$this->new_password) {
            $this->setAttributesNotValidate(['re_password']);
        }
        parent::validate();
        if ($this->isValid() && $this->current_password) {
            if (!User::get()->validatePassword($this->current_password)) {
                $this->addError('current_password', 'Неверно указан текущий пароль');
            }
        }
        return $this->getErrors();
    }

    public function save(): bool {
        $data = $this->getFormData();
        // change password.
        $new_password = ArrayHelper::getValue($data, 'new_password');
        if ($new_password) {
            User::get()->setPassword($new_password)->save();
        }
        return true;
    }
}