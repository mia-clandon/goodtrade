<?

namespace frontend\components\widgets;

use frontend\forms\site\ResetPassword as ResetPasswordForm;
use frontend\forms\site\Sign as SignForm;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class UserActions
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class UserActions extends Widget
{

    public function run()
    {
        if (!\Yii::$app->user->isGuest) {
            return '';
        }

        // форма авторизации.
        $sign_form = (new SignForm())
            ->setTitle('Авторизация')
            ->setPostMethod()
            ->setAction(Url::to('/site/sign'))
            ->setTemplatePath('@frontend/views/site')
            ->setTemplateFileName('sign')
            ->setAjaxMode()
            ->addClass('login-form')
            ->addDataAttribute('id', 'login-form');

        // форма восстановления пароля.
        $reset_password_form = (new ResetPasswordForm())
            ->setTitle('Восстановить пароль')
            ->addAttributes([
                'style' => 'display: none;',
            ])
            ->addClass('reset-password-form')
            ->setPostMethod()
            ->setAction(Url::to('/site/reset-password'))
            ->setTemplatePath('@frontend/views/site')
            ->setTemplateFileName('reset-password')
            ->addDataAttribute('id', 'forgot-mail-form')
            ->setAjaxMode();

        return $this->render('user-actions', [
            'sign_form' => $sign_form->render(),
            'reset_password_form' => $reset_password_form->render(),
        ]);
    }
}