<?php

namespace backend\forms;

use common\libs\form\components\{Button, Input};
use common\libs\form\Form;
use common\models\User;

/**
 * Class Sign
 * Форма авторизации пользователя.
 * @package backend\forms
 * @author Артём Широких kowapssupport@gmail.com
 * @property $username string
 * @property $password string
 */
class Sign extends Form
{
    private ?User $_user = null;
    private bool $remember_me = true;

    protected function initControls(): void
    {

        $username = (new Input())
            ->setTitle('Имя пользователя')
            ->setName('username')
            ->addAttributes([
                'class' => 'form-control name',
                'placeholder' => 'Имя пользователя',
                'id' => 'inputUsername',
            ])
            ->addRule(['required'])
            ->setType(Input::TYPE_TEXT);

        $this->registerControl($username);

        $password = (new Input())
            ->setTitle('Ваш пароль')
            ->setName('password')
            ->addAttributes([
                'class' => 'form-control pass',
                'placeholder' => 'Ваш пароль',
                'id' => 'inputPassword'
            ])
            ->addRule(['required'])
            ->setType(Input::TYPE_PASSWORD);

        $this->registerControl($password);

        $submit = (new Button())
            ->setName('submit')
            ->addAttributes([
                'class' => 'btn btn-primary btn-user btn-block btn-lg',
            ])
            ->setType(Button::TYPE_SUBMIT)
            ->setContent('Войти');

        $this->registerControl($submit);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }

    public function validatePassword(): void
    {
        if ($this->isValid()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Имя пользователя или пароль не верные.');
            }
        }
    }

    public function validate(): array
    {
        $result = parent::validate();
        $this->validatePassword();
        return $result;
    }

    public function sign(): bool
    {
        return \Yii::$app->user->login($this->getUser(), $this->remember_me ? 3600 * 24 * 30 : 0);
    }
}