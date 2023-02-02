<?php

namespace frontend\components\lib;

use frontend\controllers\BaseController;

/**
 * todo - переместить в namespace похожим \yii\web;
 * Class ErrorAction
 * @package frontend\components\lib
 * @author Артём Широких kowapssupport@gmail.com
 */
class ErrorAction extends \yii\web\ErrorAction {

    protected function renderHtmlResponse() {
        (new BaseController('base', null))->registerCommonBundle();
        return parent::renderHtmlResponse();
    }
}