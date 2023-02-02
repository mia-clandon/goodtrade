<?php

namespace frontend\forms\site;

use common\libs\form\Form;
use common\libs\form\validators\client\Required as ClientRequired;
use common\libs\form\validators\client\Email as ClientEmail;
use common\models\User;

use frontend\components\form\controls\Button;
use frontend\components\form\controls\Input;

/**
 * Форма авторизации пользователя. (используется в модальном окне.)
 * Class Sign
 * @package frontend\forms\site
 * @property string $email
 * @property string $password
 * @author Артём Широких kowapssupport@gmail.com
 */
class Sign extends Form {

    /** @var User|null */
    private $_user;

    /** @var bool */
    private $remember_me = true;

    public function initControls(): void {

        $email_control = (new Input())
            ->setTitle('Email')
            ->setName('email')
            ->setPlaceholder('youmail@gmail.com')
            ->setJsValidator([
                (new ClientRequired())->setErrorMessage('Введите ваш E-mail адрес.'),
                (new ClientEmail())->setErrorMessage('Некорректный формат E-mail.'),
            ])
            ->addRule(['required', 'message' => 'Введите ваш E-mail адрес.'])
            ->addRule(['email'])
        ;
        $this->registerControl($email_control);

        $password_control = (new Input())
            ->setTitle('Пароль')
            ->setType(Input::TYPE_PASSWORD)
            ->setName('password')
            ->setPlaceholder('Введите ваш пароль')
            ->setJsValidator([
                (new ClientRequired())->setErrorMessage('Введите ваш пароль.'),
            ])
            ->addRule(['required', 'message' => 'Введите ваш пароль.'])
        ;
        $this->registerControl($password_control);

        $submit_control = (new Button())
            ->setName('submit')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonColor(Button::BTN_COLOR)
            ->addClasses(['btn', 'btn-block'])
            ->setContent('Войти')
        ;
        $this->registerControl($submit_control);
    }

    /**
     * Finds user by [[username]]
     * @return User|null
     */
    protected function getUser() {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }
        return $this->_user;
    }

    public function validatePassword() {
        if ($this->isValid()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Имя пользователя или пароль не верные.');
            }
        }
    }

    public function validate(): array {
        $result = parent::validate();
        $this->validatePassword();
        return $result;
    }

    public function save(): bool {
        // авторизация.
        return \Yii::$app->user->login($this->getUser(), $this->remember_me ? 3600 * 24 * 30 : 0);
    }
}