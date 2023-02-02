<?php

namespace frontend\components\lib;

use common\libs\traits\Singleton;

/**
 * Управление хлебными крошками.
 * Class Breadcrumbs
 * @package frontend\components\lib
 * @author Артём Широких kowapssupport@gmail.com
 */
class Breadcrumbs {
    use Singleton;

    const KEY_LINK = 'link';
    const KEY_TITLE = 'title';

    protected $breadcrumbs_data = [];

    /**
     * Устанавливает данные для рендеринга хлебных крошек.
     * @param array $links ['ссылка на страницу' => 'название ссылки']
     * @return $this
     */
    public function setBreadcrumbsData(array $links) {
        foreach ($links as $link => $title) {
            $this->addBreadcrumbsLink($link, $title);
        }
        return $this;
    }

    /**
     * Добавляет хлебную крошку.
     * @param string $link
     * @param string $title
     * @return $this
     */
    public function addBreadcrumbsLink($link, $title) {
        $this->breadcrumbs_data[] = [
            self::KEY_LINK => (string)$link,
            self::KEY_TITLE => (string)$title,
        ];
        return $this;
    }

    public function render() {
        return \Yii::$app->view->renderFile(
            \Yii::getAlias('@frontend/components/lib/views/breadcrumbs.php'), [
                'breadcrumbs' => $this->breadcrumbs_data,
            ]
        );
    }
}