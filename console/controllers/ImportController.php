<?php

namespace console\controllers;

use common\libs\CsvReader;
use common\models\firms\Profile;
use Exception;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class ImportController
 * php yii import/parse-firms -p="/var/www/trade/file.csv" -l="50"
 * @package console\controllers
 */
class ImportController extends Controller
{
    /** @var string Путь */
    public string $path;
    /** @var int Количество insert, в 1 запросе */
    public int $limit = 50;

    public function options($actionID): array
    {
        return ['path', 'limit'];
    }

    public function optionAliases(): array
    {
        return [
            'p' => 'path',
            'l' => 'limit',
        ];
    }

    /**
     * Парсинг организаций и запись в базу.
     * @return bool
     * @throws Exception
     */
    public function actionParseFirms(): bool
    {
        if (!$this->path) {
            $this->stdout('Установите путь к файлу.' . "\n", Console::BOLD);
            return false;
        }

        $message_map = [
            'count_insert_queries' => 'Количество INSERT запросов.',
            'count_insert_rows' => 'Количество вставленных запросов.',
        ];

        $reader = (new CsvReader())
            ->setTableName(Profile::tableName())
            ->setFilePath($this->path)
            ->setLimit($this->limit)
            ->setHeaderMap([
                'БИН' => 'bin',
                'Наименование предприятия' => 'title',
                'ОКЭД' => 'oked',
                'Вид деятельности предприятия' => 'activity',
                'КАТО' => 'kato',
                'Населённый пункт' => 'locality',
                'КРП' => 'krp',
                'Размер предприятия' => 'company_size',
                'Руководитель' => 'leader',
                'Юр.Адрес' => 'legal_address',
                'Тел./Факс' => 'phone',
                'E-Mail' => 'email',
                'Сайт' => 'site',
            ]);

        $result_messages = $reader->save();
        foreach ($result_messages as $key => $count) {
            if (array_key_exists($key, $message_map)) {
                $this->stdout($message_map[$key] . " - " . $count . "\n", Console::BOLD);
            }
        }
        return true;
    }
}