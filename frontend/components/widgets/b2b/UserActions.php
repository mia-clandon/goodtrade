<?

namespace frontend\components\widgets\b2b;

use frontend\components\widgets\UserActions as BaseUserActions;
use frontend\forms\site\b2b\Sign;
use yii\helpers\Url;

/**
 * Class UserActions
 * @package frontend\components\b2b\widgets
 * @author yerganat
 */
class UserActions extends BaseUserActions
{

    public function run()
    {
        if (!\Yii::$app->user->isGuest) {
            return '';
        }

        // форма авторизации.
        $sign_form = (new Sign())
            ->setTitle('Авторизация')
            ->setPostMethod()
            ->setAction(Url::to('/site/sign'))
            ->setAjaxMode()
            ->addClass('login-form')
            ->addDataAttribute('id', 'login-form')
            ->setUseNewCodingMode();

        return $this->render('user-actions', [
            'sign_form' => $sign_form->render(),
        ]);
    }
}