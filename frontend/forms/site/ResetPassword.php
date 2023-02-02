<?php

namespace frontend\forms\site;

use yii\db\ActiveQuery;

use common\libs\form\Form;
use common\libs\form\validators\client\Required as ClientRequired;
use common\libs\form\validators\client\Email as ClientEmail;
use common\libs\mail\SendMail;
use common\models\User;

use frontend\components\form\controls\Button;
use frontend\components\form\controls\Input;

/**
 * Форма восстановления пароля. (используется в модальном окне.)
 * Class ResetPassword
 * @package frontend\forms\site
 * @property string $email
 * @author Артём Широких kowapssupport@gmail.com
 */
class ResetPassword extends Form {

    /** @var null|User */
    private $user;

    public function initControls(): void {

        $this->registerJsScript([
            'depends' => 'yii\web\JqueryAsset',
        ]);

        $email_control = (new Input())
            ->setTitle('Введите ваш email')
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

        $submit_control = (new Button())
            ->setName('submit')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonColor(Button::BTN_COLOR)
            ->addClasses(['btn', 'btn-block'])
            ->setContent('Выслать пароль')
        ;
        $this->registerControl($submit_control);
    }

    /**
     * @return ActiveQuery
     */
    private function getUser() {
        if (is_null($this->user)) {
            $this->user = User::find()->where(['email' => $this->email]);
        }
        return $this->user;
    }

    public function validateEmail() {
        if ($this->isValid()) {
            $has_user = $this->getUser()->count() > 0;
            if (!$has_user) {
                $this->addError('email', 'Пользователь не зарегистрирован.');
            }
        }
    }

    public function validate(): array {
        $result = parent::validate();
        $this->validateEmail();
        return $result;
    }

    public function save(): bool {
        /** @var User $user */
        $user = $this->getUser()->one();

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }

        if (!$user->save()) {
            return false;
        }

        //Высылаю ссылку на восстановление пароля.
        return SendMail::i()
            ->setUser($user)
            ->sendRecoveryPasswordLink();
    }
}