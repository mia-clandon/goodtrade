<?php

namespace frontend\modules\api\controllers;

use common\libs\sphinx\Query;
use common\libs\StringBuilder;
use common\models\firms\Profile;

use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class ProfileController
 * API контроллер для работы с профилями организаций.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 * //TODO-F: отдает поле short_title, хотя не должно.
 */
class ProfileController extends ApiController {

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
     * Возможные колонки
     * @see actionGet();
     * @return array
     */
    private function possibleColumns() {
        return [
            'id', 'title', 'bin',
        ];
    }

    /**
     * Получение данных профиля организации по id.
     * ERROR CODES:
     * 1 - Профиль организации отсутствует.
     * 2 - Отсутствуют колонки.
     * 3 - Не корректный формат колонок.
     * 0 - Успех.
     * @return array
     */
    public function actionGet() {
        $id = (int)Yii::$app->request->post('id', 0);
        $response_format = ['error' => null, 'data' => []];
        $columns = (string)Yii::$app->request->post('columns', null);
        // не прислали колонку.
        if (!$columns) {
            $response_format = ['error' => 2, 'data' => [], 'message' => 'Отсутствуют колонки.'];
            return $response_format;
        }
        else {
            $columns = explode(',', trim($columns));
            foreach ($columns as $column) {
                if (!in_array($column, $this->possibleColumns())) {
                    $response_format = ['error' => 3, 'data' => [], 'message' => 'Не корректный формат колонок.'];
                    return $response_format;
                }
            }
        }
        $profile = Profile::find()
            //short_title обязательная колонка в выборке.
            ->select(array_merge($columns, ['short_title']))
            ->where(['id' => $id])
            ->asArray()
            ->one()
        ;
        if (!$profile) {
            $response_format['error'] = 1;
            $response_format['message'] = 'Запись отсутствует.';
            return $response_format;
        }
        $response_format['data'] = $this->prepareProfileItem($profile);
        $response_format['error'] = 0;
        return $response_format;
    }

    /**
     * Поиск профилей организаций.
     * ERROR CODES:
     * 1 - запрос не может быть пустым.
     * 2 - ошибка при выполнении запроса. (пишется в лог)
     * @return array ['error']
     */
    public function actionFind() {

        $data = \Yii::$app->request->post();

        $query = ArrayHelper::getValue($data, 'query');

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
                ->from(Profile::tableName())
            ;

            if ((new Profile())->isBin($query)) {

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
                $profile_data = Profile::find()
                    ->select(['id', 'title', 'bin', 'short_title'])
                    ->where(['id' => $id_list])
                    ->orderBy(new Expression("FIND_IN_SET(id, '".implode(',',$id_list)."')"))
                    ->asArray()
                    ->all()
                ;
                $response_format['error'] = 0;
                $response_format['data'] = (array)$this->prepareProfileData($profile_data);
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
    private function prepareProfileData(array $data) {
        foreach ($data as &$item) {
            // скрываю БИН
            $item = $this->hideBin($item);
            // обработка title.
            $item = $this->prepareTitle($item);
        }
        unset($item);
        return $data;
    }

    /**
     * Обработка данных.
     * @for @see actionGet
     * @param array $item
     * @return array
     */
    private function prepareProfileItem(array $item) {
        $item = $this->prepareTitle($item);
        return $item;
    }

    /**
     * Возвращает необходимое название.
     * @param array $item
     * @return array
     */
    private function prepareTitle(array $item) {
        if (isset($item['title']) && isset($item['short_title'])) {
            $item['title'] = (!empty($item['short_title'])) ? $item['short_title'] : $item['title'];
        }
        // в ответе short_title быть не должно.
        if (isset($item['short_title'])) {
            unset($item['short_title']);
        }
        return $item;
    }

    /**
     * Скрывает БИН
     * @param array $item
     * @param string $hide_string
     * @param int $count_symbols
     * @return array
     */
    private function hideBin(array $item, $hide_string = '***', $count_symbols = 3) {
        if (isset($item['bin'])) {
            $hidden_bin = (new StringBuilder())
                ->add(substr((string)$item['bin'], 0, $count_symbols))
                ->add($hide_string)
                ->add(substr((string)$item['bin'], -1 * abs($count_symbols), 3))
            ;
            $item['bin'] = $hidden_bin->get();
        }
        return $item;
    }
}