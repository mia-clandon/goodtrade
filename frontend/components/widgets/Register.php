<?

namespace frontend\components\widgets;

use frontend\forms\site\Register as RegisterForm;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class Register
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class Register extends Widget
{

    public function run()
    {
        $controller_id = \Yii::$app->controller->id;

        if (!\Yii::$app->user->isGuest || $controller_id == 'compare' || $controller_id == 'favorite') {
            return '';
        }

        $form = (new RegisterForm())
            ->setPostMethod()
            ->setAction(Url::to('/site/register'))
            ->setTitle('Получите бесплатный доступ')
            ->setTemplatePath('@frontend/views/site')
            ->setTemplateFileName('register')
            ->setAjaxMode();

        return $this->render('register', [
            'form' => $form->render(),
        ]);
    }
}