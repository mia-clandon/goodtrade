<?php

namespace common\libs\mail;

use common\libs\Env;
use common\libs\traits\Singleton;
use common\models\User;
use Yii;

/**
 * Class SendMail
 * @package common\libs\mail
 * @author Артём Широких kowapssupport@gmail.com
 */
class SendMail
{
    use Singleton;

    //Отправитель. //todo: вынести в config.
    private const FROM_MAIL = 'support@goodtrade.kz';

    private string $to = '';
    private ?User $user = null;

    //Получатель.
    public function setTo(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    //Выполняется перед отправкой письма.
    public function canSend(): bool
    {
        //не отправляем письма не с боевого окружения.
        return Env::i()->isProd();
    }

    //Отправка пароля пользователю.
    public function sendNewPassword(string $password): bool
    {
        if (!$this->canSend()) {
            return true;
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        $user = !is_null($this->getUser())
            ? $this->user
            : Yii::$app->getUser()->getIdentity();

        return Yii::$app->mailer
            ->compose('newPassword-html', [
                'user' => $user,
                'password' => $password,
            ])
            ->setFrom(self::FROM_MAIL)
            ->setTo($this->to)
            ->setSubject('Ваш пароль для доступа на GoodTrade.kz')
            ->send();
    }

    //Отправка ссылки пользователю на восстановление пароля.
    public function sendRecoveryPasswordLink(): bool
    {
        if (!$this->canSend()) {
            return true;
        }
        $user = $this->getUser();
        return Yii::$app->mailer
            ->compose('passwordResetToken-html', [
                'user' => $user,
            ])
            ->setFrom(self::FROM_MAIL)
            ->setTo($user->email)
            ->setSubject('Восстановление пароля GoodTrade.kz')
            ->send();
    }
}
