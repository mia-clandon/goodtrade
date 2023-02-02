<?

namespace frontend\components\widgets\b2b;

use frontend\forms\site\b2b\Register as RegisterForm;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class Register
 * Новая форма регистрации (для новых страниц.).
 * @package frontend\components\widgets\b2b
 * @author yerganat
 */
class Register extends Widget
{

    public function run()
    {
        if (!\Yii::$app->user->isGuest) {
            return '';
        }

        $form = (new RegisterForm())
            ->setPostMethod()
            ->setAction(Url::to('/site/register'))
            ->setTitle('Зарегистрируйтесь и получите бесплатный доступ на 30 дней')
            ->setAjaxMode()
            ->addClass('register-form')
            ->setId('fast-register-form')
            ->setUseNewCodingMode();

        return $this->render('register', [
            'form' => $form->render(),
        ]);
    }
}