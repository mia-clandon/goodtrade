<?php

namespace frontend\forms\site\b2b;

use frontend\components\form\controls\b2b\Input;
use frontend\components\form\controls\b2b\Button;
use frontend\forms\site\Sign as BaseSign;

use common\libs\form\validators\client\Required as ClientRequired;
use common\libs\form\validators\client\Email as ClientEmail;

/**
 * Новая форма авторизации пользователя. (используется в модальном окне.)
 * Class Sign
 * @package frontend\forms\site\b2b
 * @author yerganat
 */
class Sign extends BaseSign {

    public function initControls(): void {
        $email_control = (new Input())
            ->setTitle('Email')
            ->setName('email')
            ->setPlaceholder('youmail@gmail.com')
            ->addRule(['required', 'message' => 'Введите ваш E-mail адрес.'])
            ->addRule(['email'])
            ->setJsValidator([
                (new ClientRequired())->setErrorMessage('Введите ваш E-mail адрес.'),
                (new ClientEmail())->setErrorMessage('Некорректный формат E-mail.'),
            ])
        ;
        $this->registerControl($email_control);

        $password_control = (new Input())
            ->setTitle('Пароль')
            ->setType(Input::TYPE_PASSWORD)
            ->setName('password')
            ->setPlaceholder('Введите ваш пароль')
            ->addRule(['required', 'message' => 'Введите ваш пароль.'])
            ->setJsValidator([
                (new ClientRequired())->setErrorMessage('Введите ваш пароль.'),
            ])
        ;
        $this->registerControl($password_control);

        $submit_control = (new Button())
            ->setName('submit')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_PRIMARY)
            ->setFullButton()
            ->setTitle('Войти')
        ;
        $this->registerControl($submit_control);
    }
}