<?php

namespace frontend\modules\api\controllers;

use common\models\Category;

use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class ActivityController
 * API контроллер для работы со сферами деятельности.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ActivityController extends ApiController {

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
     * Получение категорий по сфере деятельности / категории.
     * @return array
     */
    public function actionGetByCategory() {

        $data = \Yii::$app->request->post();
        $category_id = ArrayHelper::getValue($data, 'category_id', null);
        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if (null === $category_id || empty($category_id)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {

            $response_format['error'] = 0;
            $response_format['data'] = $this->getChildCategoryList($category_id);

            return $response_format;

        } catch (Exception $exception) {
            // произошла ошибка в запросах.
            Yii::error($exception->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            $response_format['message'] = $exception->getMessage();
            return $response_format;
        }
    }

    /**
     * Формирует html со списком категорий для контрола выбора категорий.
     * @see \frontend\components\form\controls\Activity
     * @return string
     * @throws Exception
     */
    public function actionGetControlCategoryList() {
        $category_id = (int)\Yii::$app->request->post('category_id');
        if (!$category_id) {
            throw new Exception('Категория не найдена.');
        }
        /** @var Category $category */
        $category = Category::findOne($category_id);
        if (!$category) {
            throw new Exception('Категория не найдена.');
        }

        $categories = [$category];
        return $this->render('activity-category-list', [
            'categories' => $categories,
        ]);
    }

    /**
     * Метод отдаёт массив с дочерними категориями.
     * @param int $parent_id
     * @return array
     */
    private function getChildCategoryList($parent_id) {
        $parent_id = (int)$parent_id;
        $sub_query = (new Query())
            ->select((new Expression('COUNT(*)')))
            ->from(Category::tableName())
            ->where(['parent' => new Expression('t.id')])
        ;
        /** @var Category[] $category_list */
        $category_list = Category::find()
            ->select(['id', 'title', 'parent',
                      new Expression('IF (('.$sub_query->createCommand()->rawSql.'), true, false) as `has_child`')
            ])
            ->where(['parent' => (int)$parent_id])
            ->orderBy('id DESC')
            ->alias('t')
            ->asArray()
            ->all();
        return $category_list;
    }

    /**
     * Получение категорий товаров по сфере деятельности.
     * @deprecated
     * @see \frontend\modules\api\controllers\ActivityController::actionGetByCategory
     * @return array
     */
    public function actionGetByActivity() {

        $data = \Yii::$app->request->post();
        /** @var string|null $activities */
        $activities = ArrayHelper::getValue($data, 'query', null);
        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if (null === $activities || empty($activities)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {
            /** @var array $activities */
            $activities = explode(',', (string)$activities);
            $activity_ids = [];
            foreach ($activities as $activity) {
                $activity = (int)trim($activity);
                if (is_numeric($activity) && $activity) {
                    $activity_ids[] = $activity;
                }
            }
            /** @var Category[] $activity_list */
            $activity_list = Category::find()
                ->select(['id', 'title', 'parent as activity'])
                ->where(['parent' => $activity_ids])
                ->orderBy('activity DESC')
                ->asArray()
                ->all()
            ;
            $response_format['error'] = 0;
            $response_format['data'] = $activity_list;
            return $response_format;

        } catch (Exception $exception) {
            // произошла ошибка в запросах.
            Yii::error($exception->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            $response_format['message'] = $exception->getMessage();
            return $response_format;
        }
    }
}