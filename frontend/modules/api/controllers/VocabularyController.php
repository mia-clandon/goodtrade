<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use common\libs\parser\Speller;
use common\libs\sphinx\Query;
use common\models\Vocabulary;

/**
 * Class VocabularyController
 * API контроллер для работы с характеристиками.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class VocabularyController extends ApiController {

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
     * Значение характеристики.
     */
    public function actionTerms() {

        $vocabulary_id = (int)Yii::$app->request->post('vocabulary_id', 0);
        $response_format = ['error' => null, 'data' => []];

        if (!$vocabulary_id) {
            $response_format['error'] = 2;
            $response_format['message'] = 'Не передан идентификатор характеристики.';
            return $response_format;
        }

        $vocabulary = Vocabulary::findOne($vocabulary_id);
        if (!$vocabulary) {
            $response_format['error'] = 3;
            $response_format['message'] = 'Характеристика не найдена.';
            return $response_format;
        }

        $term_model = $vocabulary->getTermModel();
        $terms = $term_model::find()
            ->select(['id as `value`', 'value as `label`'])
            ->where([
                'vocabulary_id' => $vocabulary_id,
            ])
            ->asArray()
            ->all()
        ;

        if (!$terms) {
            $response_format['error'] = 1;
            $response_format['message'] = 'Значений характеристики нет.';
            return $response_format;
        }

        $response_format['data'] = $terms;
        $response_format['error'] = 0;

        return $response_format;
    }

    /**
     * Поиск по характеристикам.
     * @return array
     */
    public function actionFind() {

        $data = \Yii::$app->request->post();

        $query_word = ArrayHelper::getValue($data, 'query');
        $use_speller = (bool)ArrayHelper::getValue($data, 'correcting', true);

        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if (null === $query_word || empty($query_word)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {

            $response_format['data'] = $this->findData($query_word, $use_speller);
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
     * Поиск характеристик, либо исправление не правильного ввода характеристики.
     * @param string $query_word
     * @param boolean $use_speller
     * @return array|\common\models\Vocabulary[]
     */
    private function findData($query_word, $use_speller) {

        $result = [];

        $vocabularies = (new Query())
            ->select(['id', new Expression('weight() as weight')])
            ->from(Vocabulary::tableName())
            ->setMatchTransliterate(true)
            ->andMatch(['title' => $query_word])
            ->orderBy('weight DESC')
            ->all(Yii::$app->get('sphinx'))
        ;

        if (empty($vocabularies)) {

            if (!$use_speller) {

                return $result;
            }

            $speller_result = Speller::getInstance()->get($query_word);

            // если слово было исправлено.
            if (strcasecmp($query_word, $speller_result) !== 0) {

                $result[] = [
                    'value' => 0,
                    'label' => $speller_result,
                    'corrected' => 1,
                ];
            }
        }
        else {

            $vocabulary_ids = ArrayHelper::getColumn($vocabularies, 'id', []);

            /** @var Vocabulary[] $vocabularies_list */
            $vocabularies_list = Vocabulary::find()
                ->select(['id as `value`', 'title as `label`', '0 as `corrected`'])
                ->where(['id' => $vocabulary_ids])
                ->orderBy(new Expression("FIND_IN_SET(id, '".implode(',',$vocabulary_ids)."')"))
                ->asArray()
                ->all()
            ;

            return $vocabularies_list;
        }

        return $result;
    }
}