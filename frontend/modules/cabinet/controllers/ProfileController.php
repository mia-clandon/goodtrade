<?php

namespace frontend\modules\cabinet\controllers;

use common\models\firms\Firm;
use frontend\modules\cabinet\forms\profile\Settings as SettingsForm;

/**
 * Class ProfileController
 * @package frontend\modules\cabinet\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProfileController extends DefaultController
{

    public function actionIndex()
    {
        $this->seo->title = 'Профиль пользователя';
        $this->registerScriptBundle();

        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(\Yii::$app->urlManager->createUrl(['/cabinet/profile']), 'Профиль');

        $settings_form = (new SettingsForm())
            ->setTitle('Профиль')
            ->setPostMethod()
            ->setTemplateFileName('settings')
            ->setModel(Firm::get())
            ->setAjaxMode();

        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            return $settings_form->ajaxValidateAndSave();
        }

        return $this->render('index', [
            'form' => $settings_form->render(),
        ]);
    }
}