<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class DeployController
 * @example: php yii deploy/send-deploy-result --env=dev/prod/demo
 * @package console\controllers
 */
class DeployController extends Controller {

    const LOG_FILE_NAME = 'deploy.log.txt';

    const DEPLOY_DEV = 'dev';
    const DEPLOY_DEMO = 'demo';
    const DEPLOY_PROD = 'prod';

    /** @var string */
    public $env;

    public function options($actionID) {
        return array_merge(parent::options($actionID), ['env']);
    }

    public function optionAliases() {
        return array_merge(parent::optionAliases(), [
            'e' => 'env',
        ]);
    }

    /**
     * Возвращает окружение для деплоя.
     * @return string
     */
    public function getEnv(): string {
        if (in_array($this->env, [self::DEPLOY_DEV, self::DEPLOY_DEMO, self::DEPLOY_PROD])) {
            return $this->env;
        }
        return self::DEPLOY_DEV;
    }

    /**
     * Возвращает путь до файла с логом деплоя в зависимости от окружения.
     * @return string
     */
    public function getPathToLogFile(): string {
        switch ($this->getEnv()) {
            case self::DEPLOY_DEV: {
                return '/var/www/'.self::LOG_FILE_NAME;
            }
            case self::DEPLOY_DEMO: {
                return '/home/b2bmarket/demo.goodtrade.kz/deploy/'.self::LOG_FILE_NAME;
            }
            case self::DEPLOY_PROD: {
                return '/home/b2bmarket/goodtrade.kz/deploy/'.self::LOG_FILE_NAME;
            }
        }
        return '/var/www/'.self::LOG_FILE_NAME;
    }

    /**
     * @param string $log
     * @return array - messages.
     */
    private function parseLogFile(string $log): array {

        // Проверка на незакомиченые данные.
        // Cannot pull with rebase: You have unstaged changes.
        // Please commit or stash them.

        $messages = [];

        if (mb_strpos($log, 'Please commit or stash them') !== false) {
            // Есть незакомиченые данные.
            $messages[] = '<b>Внимание ! Есть незакомиченые файлы ! Деплой невозможен !</b><br />';
        }

        if (mb_strpos($log, 'Merge conflict in') !== false) {
            // Есть конфликты.
            $messages[] = '<b>Внимание ! Возник конфликт при обновлении ветки !</b><br />';
        }

        if (mb_strpos($log, 'Exception') !== false
            || mb_strpos($log, 'throw new') !== false
            || mb_strpos($log, 'throw') !== false
        ) {
            // Всплыл exception.
            $messages[] = '<b>Внимание ! при выполнении deploy скрипта выпал Exception, посмотрите логи !</b><br />';
        }

        if (mb_strpos($log, 'Denied') !== false
            || mb_strpos($log, 'Permission') !== false
            || mb_strpos($log, 'permission') !== false
            || mb_strpos($log, 'denied') !== false
            || mb_strpos($log, 'Permission denied') !== false
        ) {
            // Вероятно нет на что то прав.
            $messages[] = '<b>Внимание ! при выполнении deploy скрипта есть вероятность ошибки прав доступа, посмотрите логи !</b><br />';
        }

        // Поиск задач
        $found_issues = [];
        preg_match_all('/issue_[0-9]{0,}/i', $log, $found_issues);
        $found_issues = ArrayHelper::getValue($found_issues, 0, []);
        $issues = [];
        foreach ($found_issues as $issue_string) {
            $issues[] = str_replace("issue_", "", $issue_string);
        }

        $issues = array_unique($issues);
        foreach ($issues as $issue_number) {
            $messages[] = "Задача под номером №".$issue_number." вылита на ".$this->getEnv().".";
        }

        //todo: добавить еще варианты парсинга файла.
        return $messages;
    }

    /**
     * Отправка письма с логами "результатом" деплоя на почту.
     */
    public function actionSendDeployResult() {
        $this->stdout("Отправка письма ...". "\n", Console::BOLD);

        if (!file_exists($this->getPathToLogFile())) {
            $this->sendDeployResult('
                Есть вероятность что deploy сломался, т.к. отсутствует файл лога '. $this->getPathToLogFile().'
            ');
        }
        else {
            $deploy_log = file_get_contents($this->getPathToLogFile());

            // результат парсинга лога.
            $messages = $this->parseLogFile($deploy_log);

            $body = implode("<br />", $messages);
            $body .= "<br /><b>Скрипт деплоя на окружении '".$this->getEnv()."' выполнен, смотрите результат в attach файле.</b><br />";

            $this->sendDeployResult($body);
        }
        $this->stdout("End...". "\n", Console::BOLD);
    }

    /**
     * @param string $text
     * @return bool
     */
    private function sendDeployResult(string $text): bool {
        $receivers = [
            'kowapssupport@gmail.com',
            //'m.dostarbekov@gmail.com',
            //'y.kerey2@gmail.com',
			'razrabotchik.www@gmail.com',
        ];
        $result = \Yii::$app->mailer->compose()
            ->setFrom('deploy@goodtrade.kz')
            ->setHtmlBody($text)
            ->setTo($receivers)
            ->setSubject('b2bMarket '.mb_strtolower($this->getEnv()).' deploy result')
            ->attach($this->getPathToLogFile(), [
                'fileName' => 'deploy_log',
                //'contentType' => 'text/html',
            ])
            ->send();

        if ($result) {
            $this->stdout("Send mail success ...". "\n", Console::BOLD);
        }
        else {
            $this->stdout("Send mail error ...". "\n", Console::BOLD);
        }
        return $result;
    }
}