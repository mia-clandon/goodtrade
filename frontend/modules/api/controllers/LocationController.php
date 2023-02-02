<?php

namespace frontend\modules\api\controllers;

use common\libs\manticore\Client;
use common\models\Location;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\VerbFilter;

/**
 * Class LocationController
 * API контроллер для работы с городами.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class LocationController extends ApiController
{
    private const FIND_LIMIT = 5;

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'find' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Поиск городов.
     * ERROR CODES:
     * 1 - запрос не может быть пустым.
     * 2 - ошибка при выполнении запроса. (пишется в лог)
     * @return array ['error']
     */
    public function actionFind(): array
    {
        $data = \Yii::$app->request->post();

        $query = arr_get_val($data, 'query');
        $limit = arr_get_val($data, 'limit', self::FIND_LIMIT);
        $limit = $limit > self::FIND_LIMIT ? self::FIND_LIMIT : $limit;

        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if (null === $query || empty($query)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {

            $result = (new Client())->search(Location::indexName(), "*{$query}*");
            if ($result->getTotal()) {

                $location_data = Location::find()
                    ->select(['id', 'title', 'region as region_id'])
                    ->where(['id' => $result->getIds()])
                    ->orderBy(new Expression("FIND_IN_SET(id, '" . implode(',', $result->getIds()) . "')"))
                    ->asArray()
                    ->all();

                $response_format['error'] = 0;
                $response_format['data'] = (array)$this->prepareLocationData($location_data);
                return $response_format;
            }

            $response_format['error'] = 0;
            return $response_format;

        } catch (Exception $e) {
            // произошла ошибка в запросах.
            Yii::error($e->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            return $response_format;
        }
    }

    /**
     * Обработка данных.
     * @for @see actionFind()
     * @param array $data
     * @return array
     */
    private function prepareLocationData(array $data): array
    {
        $region_map = (new Location())->getPossibleRegions();
        $region_map = array_flip($region_map);
        foreach ($data as &$item) {
            // добавляю область к названию города.
            if (isset($item['title']) && $item['region_id'] > 0) {
                $region = arr_get_val($region_map, $item['region_id'], '');
                $item['region'] = $region;
            }
        }
        unset($item);
        return $data;
    }
}
