<?php

namespace console\controllers;

use backend\components\CleanProcessor;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class CleanController
 * php yii clean/relations
 * @package console\controllers
 */
class CleanController extends Controller {

    /**
     * Запуск очистки битых связей.
     */
    public function actionRelations() {
        $this->stdout("Начало очистки связей ...". "\n", Console::BOLD);
        CleanProcessor::i()->clean();
        $this->stdout("End...". "\n", Console::BOLD);
    }

}