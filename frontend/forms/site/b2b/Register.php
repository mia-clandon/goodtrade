<?php

namespace frontend\forms\site\b2b;

use common\libs\form\validators\client\{Email as ClientEmail, Required as ClientRequired};
use frontend\components\form\controls\b2b\{Button, Checkbox, Input};
use frontend\forms\site\Register as BaseRegister;

/**
 * Форма авторизации пользователя. (используется в модальном окне.)
 * Class Register
 * @package frontend\forms\site\b2b
 * @author yerganat
 */
class Register extends BaseRegister
{
    protected function initControls(): void
    {
        $phone_component = (new Input())
            ->setTitle('Ваш основной телефон')
            ->setName('phone')
            ->setId('input-tel')
            ->setPlaceholder('+7 (777) 987 65 43')
            ->setInputType(Input::PHONE_TYPE)
            ->setJsValidator([
                (new ClientRequired())->setErrorMessage('Введите ваш номер телефона.')
            ])
            ->addRule(['required', 'message' => 'Введите ваш номер телефона.']);
        $this->registerControl($phone_component);

        $email_component = (new Input())
            ->setTitle('Ваш основной email')
            ->setName('email')
            ->setPlaceholder('username@gmail.com')
            ->setInputType(Input::EMAIL_TYPE)
            ->setJsValidator([
                (new ClientRequired())->setErrorMessage('Введите ваш E-mail адрес.'),
                (new ClientEmail())->setErrorMessage('Некорректный формат E-mail.'),
            ])
            ->addRule(['required', 'message' => 'Введите ваш E-mail адрес.']);
        $this->registerControl($email_component);

        $bin_component = (new Input())
            ->setTitle('Ваш БИН')
            ->setName('bin')
            ->setPlaceholder('1234567890')
            ->setInputType(Input::BIN_TYPE)
            ->addRule(['number'])
            ->addRule(['string', 'max' => 12, 'min' => 12,]);
        $this->registerControl($bin_component);

        $button_component = (new Button())
            ->setName('submit')
            ->setTitle('Зарегистрироваться')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_PRIMARY)
            ->setFullButton();
        $this->registerControl($button_component);

        $confidentiality = (new Checkbox())
            ->addClass('checkbox__input')
            ->setId('cbx-confidentiality')
            ->setName('confidentiality')
            ->setTitle('Я даю свое согласие на обработку персональных данных и соглашаюсь с политикой конфиденциальности')
            ->setChecked();
        $this->registerControl($confidentiality);

        $subscription = (new Checkbox())
            ->addClass('checkbox__input')
            ->setId('cbx-subscription')
            ->setName('subscription')
            ->setTitle('Я хочу получать email-письма о мероприятиях и/или иных услугах')
            ->setChecked();
        $this->registerControl($subscription);
    }
}