<?php

namespace frontend\components\lib\b2b;

/**
 * Управление хлебными крошками.
 * Class Breadcrumbs
 * @package frontend\components\b2b\lib
 * @author yerganat
 */

class Breadcrumbs  extends \frontend\components\lib\Breadcrumbs {

    const KEY_ICON = 'icon';

    /**
     * Добавляет хлебную крошку.
     * @param string $link
     * @param string $title
     * @param string $icon
     * @return $this
     */
    public function addBreadcrumbsLink($link, $title, $icon = null) {
        $this->breadcrumbs_data[] = [
            self::KEY_LINK => (string)$link,
            self::KEY_TITLE => (string)$title,
            self::KEY_ICON => $icon,
        ];
        return $this;
    }

    public function render() {
        return \Yii::$app->view->renderFile(
            \Yii::getAlias('@frontend/components/lib/views/b2b/breadcrumbs.php'), [
                'breadcrumbs' => $this->breadcrumbs_data,
            ]
        );
    }
}