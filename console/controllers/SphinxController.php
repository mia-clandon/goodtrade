<?php

namespace console\controllers;

use common\models\Location;
use common\models\Oked;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class SphinxController
 * @package console\controllers
 * php yii sphinx/reindex
 */
class SphinxController extends Controller {

    /** @var string|null */
    public $index;

    public function options($action_id) {
        return ['index'];
    }

    public function optionAliases() {
        return [
            'i' => 'index',
        ];
    }

    /**
     * @return array
     */
    private function getPossibleIndexes(): array {
        return ['location', 'oked', 'firms_profiles', 'firm', 'product', 'vocabulary'];
    }

    private function updateIndexesAfterConfigChanges(): void {
//        sudo searchd --stop --config /var/www/trade/sphinx_source.conf
//        /etc/sphinxsearch/data/trade/rt && sudo rm -r -f *
//        sudo searchd --config /var/www/trade/sphinx_source.conf
    }

    /**
     * @param string $index
     * @param bool $run_all
     */
    private function runReindex(bool $run_all = false, string $index = ''): void {
        if ($index === 'location' || $run_all) {
            Location::indexAll();
        }
        if ($index === 'oked' || $run_all) {
            Oked::indexAll();
        }
        //todo: firms_profiles
        //todo: firm
        //todo: product
        //todo: vocabulary
    }

    /**
     * Запускает переиндексацию Sphinx.
     */
    public function actionReindex() {
        $this->stdout("Начало переиндексации ...". "\n", Console::BOLD);
        $is_all = null === $this->index;
        try {
            if ($is_all === false && \in_array((string)$this->index, $this->getPossibleIndexes(), true)) {
                $this->runReindex(false, $this->index);
            }
            $this->runReindex(true);
            $this->stdout("Все ОК ...". "\n", Console::BOLD);
        }
        catch (\Exception $exception) {
            $this->stdout("Ошибка выполнения ". $exception->getMessage(). "\n", Console::BOLD);
        }
    }
}