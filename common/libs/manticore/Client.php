<?php

namespace common\libs\manticore;

use Manticoresearch\{Client as ManticoreClient, Index, Query\BoolQuery, Search};
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * Class Client
 * @package common\libs\manticore
 */
class Client
{
    #region - типы данных.
    public const TYPE_INT = 'int';
    public const TYPE_BIGINT = 'bigint';
    public const TYPE_STR = 'text';
    public const TYPE_FLOAT = 'float';
    public const TYPE_JSON = 'json';
    public const TYPE_MVA = 'multi';
    #endregion.

    private ?Search $search = null;
    private ?ManticoreClient $client = null;

    //Создание нового индекса.
    public function createIndex(string $index_name, array $fields, array $options = []): bool
    {
        $index = new Index($this->getClient());
        $index->setName(trim($index_name));
        if (empty($options)) {
            $options = [
                'rt_mem_limit' => '1024M',
                'min_infix_len' => '3',
            ];
        }
        $result = $index->create($fields, $options);
        return isset($result['error']) && empty($result['error']);
    }

    //Добавление / обновление записи в индексе.
    public function saveRow(string $index, array $row): bool
    {
        if (!isset($row['id'])) {
            return false;
        }
        $this->getBuilder()->save($index, (int)$row['id'], $row);
        return true;
    }

    //Удаление записи в индексе.
    public function deleteRow(string $index, int $id): bool
    {
        $this->getBuilder()->deleteById($index, $id);
        return true;
    }

    //@see match https://github.com/manticoresoftware/manticoresearch-php/blob/master/docs/searchclass.md
    public function match(string $index, string|array $query, ?string $fields = null): Result
    {
        $search = $this->getSearch();
        $search->setIndex($index);
        $rows = [];
        $response = $search->match($query, $fields)->get();
        foreach ($response as $result) {
            $rows[] = $result->getData();
        }
        return (new Result())
            ->setTotal($response->getTotal())
            ->setIds(ArrayHelper::getColumn($rows, 'm_id', []))
            ->setRawResult($response)
            ->setRows($rows);
    }

    //@see search https://github.com/manticoresoftware/manticoresearch-php/blob/master/docs/searchclass.md
    public function search(string $index, string|BoolQuery $query): Result
    {
        $search = $this->getSearch();
        $search->setIndex($index);
        $rows = [];
        $response = $search->search($query)->get();
        foreach ($response as $result) {
            $rows[] = $result->getData();
        }
        return (new Result())
            ->setTotal($response->getTotal())
            ->setIds(ArrayHelper::getColumn($rows, 'm_id', []))
            ->setRawResult($response)
            ->setRows($rows);
    }

    public function getSearchObject(): Search
    {
        return $this->getSearch();
    }

    public function dropIndex(string $index_name): bool
    {
        $result = $this->getClient()->indices()->drop(['index' => $index_name]);
        return isset($result['error']) && empty($result['error']);
    }

    public function dropIndexIfExist(string $index_name): bool
    {
        if ($this->hasIndex($index_name)) {
            return $this->dropIndex($index_name);
        }
        return true;
    }

    public function hasIndex(string $index_name): bool
    {
        return !empty($this->status($index_name));
    }

    public function status(string $index): array
    {
        try {
            return $this->getClient()->indices()->status(['index' => $index]);
        } catch (\Exception $exception) {
            return [];
        }
    }

    private function getClient(): ManticoreClient
    {
        if ($this->client === null) {
            $this->client = new ManticoreClient([
                'host' => env('MANTICORE_HOST'),
                'port' => (int)env('MANTICORE_PORT'),
            ]);
        }
        return $this->client;
    }

    private function getSearch(): Search
    {
        if ($this->search === null) {
            $this->search = new Search($this->getClient());
        }
        return $this->search;
    }

    private function getConnection(): Connection
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return \Yii::$app->get('manticore');
    }

    private function getBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->getConnection());
    }
}