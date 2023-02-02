<?php

namespace frontend\controllers;

use yii\base\Exception;

use common\models\Page;

/**
 * Class PageController
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class PageController extends BaseController {

    /**
     * Отображение страницы сайта.
     * @throws Exception
     * @return string
     */
    public function actionShow() {
        $this->layout = 'b2b';
        $this->registerScriptBundleB2B();

        $page_alias = \Yii::$app->request->get('page');
        if (!$page_alias) {
            throw new Exception('Страница не найдена');
        }
        /** @var Page $page */
        $page = Page::find()->where(['alias' => $page_alias])
            ->one();
        /** @var Page[] $pages */
        $pages = Page::find()->orderBy('id ASC')->all();
        if (!$page) {
            throw new Exception('Страница не найдена');
        }
        $this->getBreadcrumbsB2B()
            ->addBreadcrumbsLink(
                \Yii::$app->urlManager->createUrl(['page/show', 'page' => $page_alias]),
                $page->title
            );
        $this->seo->title = $page->title;
        return $this->render('show', [
            'page'  => $page,
            'pages' => $pages,
        ]);
    }
}