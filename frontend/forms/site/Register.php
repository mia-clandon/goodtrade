<?php

namespace frontend\forms\site;

use common\libs\Env;
use common\libs\form\Form;
use common\libs\form\validators\client\{Email as ClientEmail, Required as ClientRequired};
use common\libs\mail\SendMail;
use common\models\firms\Firm;
use common\models\User;
use frontend\components\form\controls\{Button, Checkbox, Input};
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Register
 * Форма регистрации для старой вёрстки.
 * todo: в последствии выпилить форму.
 * Форма регистрации в виджите регистрации.
 * Используется на главной странице.
 * @property string|null $email
 * @property string|null $phone
 * @property int $bin
 * @property boolean $confidentiality
 * @property boolean $subscription
 * @author Артём Широких kowapssupport@gmail.com
 */
class Register extends Form
{
    private ?Input $phone_control = null;
    private ?Input $email_control = null;
    private ?Input $bin_control = null;
    private ?Button $submit_control = null;
    private ?Checkbox $confidentiality_control = null;
    private ?Checkbox $subscription_control = null;

    protected function initControls(): void
    {
        $this->setId('fast-register-form');
        $this->addClass('col-xs-5 col-md-4 col-lg-3 col-center');

        $this->registerControl($this->getPhoneControl());
        $this->registerControl($this->getEmailControl());
        $this->registerControl($this->getBinControl());
        $this->registerControl($this->getButtonControl());
        $this->registerControl($this->getConfidentialityCheckbox());
        $this->registerControl($this->getSubscriptionControl());
    }

    protected function getPhoneControl(): Input
    {
        if (null === $this->phone_control) {
            $this->phone_control = (new Input())
                ->setTitle('Ваш основной телефон')
                ->setName('phone')
                ->setId('input-tel')
                ->setPlaceholder('+7 (777) 707 00 77')
                ->setInputType(Input::PHONE_TYPE)
                ->setJsValidator([
                    (new ClientRequired())->setErrorMessage('Введите ваш номер телефона.')
                ])
                ->addRule(['required', 'message' => 'Введите ваш номер телефона.']);
        }
        return $this->phone_control;
    }

    protected function getEmailControl(): Input
    {
        if (null === $this->email_control) {
            $this->email_control = (new Input())
                ->setTitle('Ваш основной email')
                ->setName('email')
                ->setPlaceholder('youmail@gmail.com')
                ->setInputType(Input::EMAIL_TYPE)
                ->setJsValidator([
                    (new ClientRequired())->setErrorMessage('Введите ваш E-mail адрес.'),
                    (new ClientEmail())->setErrorMessage('Некорректный формат E-mail.'),
                ])
                ->addRule(['required', 'message' => 'Введите ваш E-mail адрес.']);
        }
        return $this->email_control;
    }

    protected function getBinControl(): Input
    {
        if (null === $this->bin_control) {
            $this->bin_control = (new Input())
                ->setTitle('Ваш БИН')
                ->setName('bin')
                ->setPlaceholder('1234567890')
                ->addAttribute('maxlength', 12)
                ->addAttribute('minlength', 12)
                ->setInputType(Input::BIN_TYPE);
        }
        return $this->bin_control;
    }

    protected function getButtonControl(): Button
    {
        if (null === $this->submit_control) {
            $this->submit_control = (new Button())
                ->setName('submit')
                ->setContent('Получить доступ <span class="text-md-hidden">&mdash; это бесплатно</span>')
                ->setType(Button::TYPE_SUBMIT)
                ->setButtonColor(Button::BTN_COLOR)
                ->addClasses(['btn', 'btn-block']);
        }
        return $this->submit_control;
    }

    protected function getConfidentialityCheckbox(): Checkbox
    {
        if (null === $this->confidentiality_control) {
            $this->confidentiality_control = (new Checkbox())
                ->setId('cbx-confidentiality')
                ->setName('confidentiality')
                ->setTitle('Я даю свое согласие на обработку персональных данных и соглашаюсь с условиями и политикой конфиденциальности')
                ->setChecked();
        }
        return $this->confidentiality_control;
    }

    protected function getSubscriptionControl(): Checkbox
    {
        if (null === $this->subscription_control) {
            $this->subscription_control = (new Checkbox())
                ->setId('cbx-subscription')
                ->setName('subscription')
                ->setTitle('Я хочу получать email письма о мероприятих и новых услугах')
                ->setChecked();
        }
        return $this->subscription_control;
    }

    public function validate(): array
    {
        $result = parent::validate();
        if ($this->isValid()) {
            // проверка email
            if (!$this->validateUsername()) {
                $this->addError('email', 'E-mail адрес уже используется');
            }
            // проверка телефона
            if (!$this->validatePhone()) {
                $this->addError('phone', 'Пользователь с таким номером уже зарегистрирован');
            }
            // проверка, отметил ли пользователь галку о том что согласен на обработку персональных данных.
            if (!$this->validateConfidentiality()) {
                $this->addError('confidentiality', 'Вы должны согласиться с условиями площадки');
            }
            // проверка, существует ли организация с таким БИН.
            if (!$this->validateBin()) {
                $this->addError('bin', 'Организация с таким БИН уже существует на площадке');
            }
        }
        return $result;
    }

    //Метод проверяет, существует ли уже пользователь с таким Email.
    private function validateUsername(): bool
    {
        $username = (string)$this->email;
        return !(bool)User::findByUsername($username);
    }

    //Метод проверяет, существует ли уже пользователь с таким номером телефона.
    private function validatePhone(): bool
    {
        /** @var User $user */
        $user = $this->getModel();
        $phone = $user->preparePhone((string)$this->phone);
        return !(bool)User::findByPhone($phone);
    }

    //Метод проверяет, существует ли организация с таким БИН.
    private function validateBin(): bool
    {
        if (is_numeric($this->bin)) {
            return (bool)Firm::find()->where(['bin' => (int)$this->bin])->count() == 0;
        }
        return true;
    }

    //Проверка, согласен ли пользователь с обработкой данных.
    private function validateConfidentiality(): bool
    {
        return !$this->confidentiality == false;
    }

    private function getPassword(): string
    {
        if (!Env::i()->isProd()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            return ArrayHelper::getValue(Yii::$app->params, 'defaultUserPassword');
        }
        return User::generateRandomPassword();
    }

    //Регистрация пользователя в системе.
    public function save(): bool
    {
        /** @var User $user */
        $user = $this->getModel();
        $user->setPhone($this->phone);
        $user->setAttributes([
            'username' => $this->email,
            'email' => $this->email,
            'status' => User::STATUS_ACTIVE,
            'temp_bin' => $this->bin,
        ]);

        $password = $this->getPassword();
        $user->setPassword($password);

        if ($user->save()) {
            $profile = $user->getProfile();
            $profile->setAttributes([
                'notify_email' => $this->subscription,
            ]);
            $profile->save();
            $result = Yii::$app->user->login($user, 3600 * 24 * 30);
            if ($result) {
                SendMail::i()->setTo($this->email)
                    ->sendNewPassword($password);
            }
            return $result;
        }
        return false;
    }
}
