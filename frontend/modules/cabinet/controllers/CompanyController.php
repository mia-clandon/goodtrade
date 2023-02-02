<?php

namespace frontend\modules\cabinet\controllers;

use common\models\firms\Firm;
use frontend\modules\cabinet\forms\company\Settings as SettingsForm;

/**
 * Class CompanyController
 * @package frontend\modules\cabinet\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class CompanyController extends DefaultController
{

    public function actionIndex()
    {
        $this->seo->title = 'Настройки организации';
        $this->registerScriptBundle();

        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(\Yii::$app->urlManager->createUrl(['/cabinet/company']), 'Компания');

        $settings_form = (new SettingsForm())
            ->setTitle('Компания')
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