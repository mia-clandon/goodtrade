<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use common\models\Oked;
use common\models\CategoryOked;

/**
 * Class OkedController
 * API контроллер для работы с ОКЭД.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class OkedController extends ApiController {

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
     * Получение ОКЭД'ов.
     * @return array
     */
    public function actionGet() {

        $data = \Yii::$app->request->post();
        // окед.
        $query = ArrayHelper::getValue($data, 'key', false);

        // диапазон окедов.
        $name = '';
        $from = $to = 0;
        if (!$query) {
            $name   = (string)ArrayHelper::getValue($data, 'name');
            $from   = (int)ArrayHelper::getValue($data, 'from', 0);
            $to     = (int)ArrayHelper::getValue($data, 'to', 0);
        }

        $category_id = (int)ArrayHelper::getValue($data, 'category_id', false);

        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if ((!$query) && !($from && $to) && empty($name)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {
            $data = [];

            // если пришел key - то отдаю по ключу один ОКЭД.
            if ($query) {
                /** @var Oked $oked */
                $oked = Oked::find()->where(['key' => $query])
                    ->one();
                if ($oked) {
                    $data[] = [
                        'key' => $oked->key,
                        'name' => $oked->name,
                    ];
                }
            }
            else {
                $list = Oked::find();
                // поиск по диапазону.
                if ($from !== 0) {
                    $list->andWhere(['>=', 'key', $from]);
                }
                if ($to !== 0) {
                    $list->andWhere(['<=', 'key', $to]);
                }
                // поиск по названию.
                $oked_found_by_name = [];
                if (null !== $name && !empty($name)) {
                    $oked_search = new \common\models\search\Oked();
                    $oked_search->setFilterTitle($name);
                    $oked_found_by_name = $oked_search->findIdList();
                }
                if (!empty($oked_found_by_name)) {
                    $list->andWhere(['id' => $oked_found_by_name]);
                }
                if ($category_id) {
                    // если пришла категория (отсеиваю с выборки уже выбранные ОКЭД).
                    $sub_select = CategoryOked::find()->select(['oked'])
                        ->where(['category_id' => $category_id]);
                    $list->andWhere(['NOT IN', 'key', $sub_select]);
                }
                $list = $list->all();
                /** @var Oked[] $list */
                foreach ($list as $oked) {
                    $data[] = [
                        'key' => $oked->key,
                        'name' => $oked->name,
                    ];
                }
            }

            $response_format['error'] = 0;
            $response_format['data'] = (array)$data;
            return $response_format;

        } catch (Exception $e) {
            // произошла ошибка в запросах.
            Yii::error($e->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            return $response_format;
        }
    }
}