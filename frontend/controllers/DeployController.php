<?php

namespace frontend\controllers;

use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Class DeployController
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class DeployController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'deploy'  => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        $this->layout = false;
        \Yii::$app->controller->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    private function logDeployError(string $error_message): void {
        $deploy_file_name = 'deploy-error-'.date('d.m.Y H:i:s', time()).'.txt';
        file_put_contents(\Yii::getAlias('@frontend/runtime/'). $deploy_file_name, $error_message);
    }

    /**
     * Получение массива данных с bitbucket.
     * @return array|null
     */
    private function getDeployInfo(): ?array {
        $input_bit_bucket_json = file_get_contents('php://input');
        try {
            $data = Json::decode($input_bit_bucket_json);
        }
        catch (Exception $exception) {
            $data = [];
        }
        if (!is_array($data)) {
            $data = [];
        }
        if (empty($data)) {
            $this->logDeployError('Пришли пустые данные с bitbucket.');
            return null;
        }
        return $data;
    }

    /**
     * Запуск скрипта deploy.sh
     */
    public function actionDeploy() {
        /** @var array $deploy_info */
        $deploy_info = $this->getDeployInfo();
        if ($deploy_info === null) {
            return false;
        }
        $changes = ArrayHelper::getValue($deploy_info, 'push.changes', []);
        if (empty($changes)) {
            return false;
        }
        $branch_name = ArrayHelper::getValue($changes, '0.new.name');
        if ($branch_name !== 'demo') {
            return false;
        }

        $commits = ArrayHelper::getValue($changes, '0.commits');
        $commits_messages = ArrayHelper::getColumn($commits, 'message');
        $commits_messages = implode('<br />', $commits_messages);

        // put commit messages in deploy log.
        $deploy_log_path = \Yii::getAlias('@webroot/../../deploy/'.\console\controllers\DeployController::LOG_FILE_NAME);
        file_put_contents($deploy_log_path, print_r($commits_messages, true). '<br />');

        // run deploy:
        putenv("PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/snap/bin");
        shell_exec('/home/b2bmarket/demo.goodtrade.kz/deploy.sh');
        return true;
    }
}