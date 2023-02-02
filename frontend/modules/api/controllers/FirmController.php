<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use common\libs\sphinx\Query;
use common\models\firms\Firm;

/**
 * Class FirmController
 * API контроллер для работы организациями.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class FirmController extends ApiController {

    const FIND_LIMIT = 3;

    public function behaviors() {
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
     * Поиск организаций.
     * ERROR CODES:
     * 1 - запрос не может быть пустым.
     * 2 - ошибка при выполнении запроса. (пишется в лог)
     * @return array ['error']
     */
    public function actionFind() {

        $data = \Yii::$app->request->post();

        $query = ArrayHelper::getValue($data, 'query');
        $format = ArrayHelper::getValue($data, 'format');

        $limit = ArrayHelper::getValue($data, 'limit', self::FIND_LIMIT);
        $limit = $limit > self::FIND_LIMIT ? self::FIND_LIMIT : $limit;

        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if (null === $query || empty($query)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {

            $query_ql = (new Query())
                ->select(['id', new Expression('weight() as weight')])
                ->from(Firm::tableName())
            ;

            if ((new Firm())->isBIN($query)) {

                $query_ql->andWhere(['bin' => (int)$query]);
            }
            else {

                $query_ql->setMatchTransliterate(true)
                    ->andMatch(['title' => $query]);
            }

            $id_list = $query_ql->orderBy('weight DESC')
                ->limit($limit)
                ->all(Yii::$app->get('sphinx'));

            if (!empty($id_list)) {

                $id_list = ArrayHelper::getColumn($id_list, 'id');
                $firm_query = Firm::find()
                    ->select(['id', 'title', 'bin',])
                    ->where(['id' => $id_list])
                    ->orderBy(new Expression("FIND_IN_SET(id, '".implode(',',$id_list)."')"))
                ;
                // формат "bin_with_title" - для Backend контрола.
                if ($format && $format == "bin_with_title") {
                    $firm_query->select(['id', 'CONCAT(bin, \' - \', title) AS title', 'bin',]);
                }
                $firm_data = $firm_query
                    ->asArray()
                    ->all();
                $response_format['error'] = 0;
                $response_format['data'] = (array)$this->prepareFirmData($firm_data);
                return $response_format;
            }

            $response_format['error'] = 0;
            return $response_format;

        } catch (Exception $e) {
            // произошла ошибка в запросах.
            Yii::error($e->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            $response_format['message'] = $e->getMessage();
            return $response_format;
        }
    }

    /**
     * Обработка данных.
     * @for @see actionFind()
     * @param array $data
     * @return array
     */
    private function prepareFirmData(array $data) {
        return $data;
    }
}