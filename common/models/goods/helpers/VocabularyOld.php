<?php

namespace common\models\goods\helpers;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\models\goods\VocabulariesTerm;
use common\models\VocabularyOption;
use common\models\Vocabulary as VocabularyModel;

/**
 * Хелпер для работы с характеристиками товара.
 * Class Vocabulary
 * TODO: пересмотреть класс. (удалить)
 * @author Артём Широких kowapssupport@gmail.com
 */
class VocabularyOld extends Base {

    /** Ключ для кеширования характеристик товара. */
    const CACHE_PRODUCT_VOCABULARY_TERM_DATA = 'cache_product_vocabulary_option_';

    const VOCABULARY_KEY = 'vocabulary';
    const TERMS_KEY = 'terms';
    const PROP_VOCABULARY_ID = 'id';
    const PROP_VOCABULARY_TITLE = 'title';
    const PROP_VOCABULARY_NAME = 'name';

    /** @var array */
    public $vocabulary_terms = NULL;

    /** @var bool будет создаваться активная характеристика ? */
    private $is_active_create_vocabulary = false;

    /** @var bool будет создаваться активное значение характеристики ? */
    private $is_active_create_term = false;

    /**
     * @param array $vocabulary_terms - example: [
     *      'vocabulary' => [
     *          'id' => '123' // не обязательный параметр. (если есть id, не будет поиска в базе)
     *          'name' => 'ширина',
     *      ],
     *      'terms' => [
     *          [
     *              'id' => '123', // не обязательный параметр. (если есть id, не будет поиска в базе)
     *              'name' => '1 метр',
     *          ] ...
     *      ]
     * ]...
     * @return $this
     */
    public function setVocabularyTerms(array $vocabulary_terms) {
        $this->vocabulary_terms = $vocabulary_terms;
        $this->clearCache();
        return $this;
    }

    /**
     * Метод обновляет характеристики товара и их значения.
     * @param bool $remove_not_exist - удалять из базы характеристики которые есть в базе данных
     *  но нет в массиве $this->vocabulary_terms.
     * @return bool
     */
    public function updateVocabularies($remove_not_exist = true) {

        // не установили характеристики для обновления.
        if (is_null($this->vocabulary_terms)) {
            return false;
        }

        $this->clearCache();

        $vocabulary_term_data = (array)$this->vocabulary_terms;
        $vocabulary_term_data = $this->prepareVocabularyTermData($vocabulary_term_data);

        if ($remove_not_exist) {
            $this->removeVocabularyTermRelations();
        }

        $errors = [];
        foreach ($vocabulary_term_data as $item) {
            $vocabulary_id = ArrayHelper::getValue($item, 'vocabulary');
            $vocabulary_id = intval($vocabulary_id);
            $terms = ArrayHelper::getValue($item, 'terms', []);
            foreach ($terms as $term) {
                $relation = new VocabulariesTerm();
                $relation->product_id = $this->getProductModel()->id;
                $relation->vocabulary_id = $vocabulary_id;
                $relation->term = intval($term);
                if (!$relation->save()) {
                    $errors = array_merge($errors, $relation->getErrors());
                }
            }
        }
        return (empty($errors));
    }

    /**
     * Удаление связей с характеристиками и их значениями.
     * @return int
     */
    public function removeVocabularyTermRelations() {
        return VocabulariesTerm::deleteAll(['product_id' => $this->getProductModel()->id]);
    }

    /**
     * Метод проверяет данные в массиве характеристик,
     * если переданная характеристика без id но такая уже есть в базе - отдаёт ее id,
     * если переданная характеристика без id и её нет в базе - создаёт и отдаёт ее id,
     * аналогично работает с значениями характеристик.
     * @param array $vocabulary_term_data
     * @deprecated
     * TODO: т.к. значения характеристик хранятся в 2х таблицах допилить метод.
     * @return array
     */
    private function prepareVocabularyTermData(array $vocabulary_term_data) {
        $out = [];
        foreach ($vocabulary_term_data as $item) {
            $vocabulary_id = ArrayHelper::getValue($item, 'vocabulary.id', false);
            if (!$vocabulary_id) {
                // характеристика без переданного id, ищу у нас в базе.
                $vocabulary_name = ArrayHelper::getValue($item, 'vocabulary.name', '');
                $vocabulary_name = trim($vocabulary_name);
                if (!$vocabulary_name || empty($vocabulary_name)) {
                    continue;
                }
                /** @var VocabularyModel $found_vocabulary */
                $found_vocabulary = VocabularyModel::find()->where(['title' => $vocabulary_name])->one();
                // нашлась характеристика у нас в базе.
                if ($found_vocabulary) {
                    $vocabulary_id = $found_vocabulary->id;
                }
                else {
                    $new_vocabulary = new VocabularyModel();
                    $new_vocabulary->title = $vocabulary_name;
                    if (!$new_vocabulary->save()) {
                        continue;
                    }
                    $vocabulary_id = $new_vocabulary->id;
                }
            }
            else {
                // id характеристики известен но есть ли такая у нас.
                /** @var VocabularyModel $found_vocabulary */
                $found_vocabulary = VocabularyModel::findOne($vocabulary_id);
                if (!$found_vocabulary || empty($found_vocabulary)) {
                    continue;
                }
            }

            $terms = ArrayHelper::getValue($item, 'terms', []);
            $out_terms = [];
            foreach ($terms as $term) {
                $term_id = ArrayHelper::getValue($term, 'id', false);
                if (!$term_id) {
                    // значение без переданного id, ищу у нас в базе.
                    $term_name = ArrayHelper::getValue($term, 'name', '');
                    $term_name = trim($term_name);
                    if (!$term_name) {
                        continue;
                    }
                    /** @var VocabularyOption $found_term */
                    $found_term = VocabularyOption::find()->where(['value' => $term_name])->one();
                    if ($found_term) {
                        $term_id = $found_term->id;
                    }
                    else {
                        $new_term = new VocabularyOption();
                        $new_term->value = $term_name;
                        $new_term->vocabulary_id = $vocabulary_id;
                        if (!$new_term->save()) {
                            continue;
                        }
                        $term_id = $new_term->id;
                    }
                }
                $out_terms[] = intval($term_id);
            }

            $out[] = [
                'vocabulary' => intval($vocabulary_id),
                'terms' => $out_terms,
            ];
        }
        return $out;
    }

    /**
     * Возвращает данные о характеристиках.
     * [
     *      'vocabulary' => Vocabulary
     *      'terms'      => Term[]
     * ]
     * @return array
     */
    public function getVocabularyTerms() {
        $key = self::CACHE_PRODUCT_VOCABULARY_TERM_DATA. $this->getProductModel()->id;
        $product_model = $this->getProductModel();
        if (!$vocabulary_data = \Yii::$app->cache->get($key)) {
            $vocabulary_data = [];
            /** @var ActiveQuery $vocabulary_query */
            $vocabulary_query = $product_model->getVocabularies();
            /** @var VocabularyModel[] $vocabularies */
            $vocabularies = $vocabulary_query->all();
            foreach ($vocabularies as $vocabulary) {
                $terms = $product_model->getTerms($vocabulary->id);
                $vocabulary_data[] = [
                    self::VOCABULARY_KEY    => $vocabulary,
                    self::TERMS_KEY         => $terms->all(),
                ];
            }
            \Yii::$app->cache->set($key, $vocabulary_data);
        }
        return $vocabulary_data;
    }

    /**
     * Метод возвращает характеристики товара и их значения массивом.
     * @return array
     */
    public function getVocabularyTermsArray() {
        $vocabulary_term_data = $this->getVocabularyTerms();
        $data = [];
        foreach ($vocabulary_term_data as $vocabulary_term) {
            /** @var \common\models\Vocabulary $vocabulary */
            $vocabulary = $vocabulary_term[self::VOCABULARY_KEY];
            $terms = $vocabulary_term[self::TERMS_KEY];
            $terms_titles = [];
            /** @var \common\models\VocabularyOption[] $terms */
            foreach ($terms as $term) {
                $terms_titles[$term->id] = $term->value;
            }
            $data[] = [
                self::VOCABULARY_KEY => [
                    self::PROP_VOCABULARY_ID => $vocabulary->id,
                    self::PROP_VOCABULARY_TITLE => $vocabulary->title,
                ],
                self::TERMS_KEY => $terms_titles,
            ];
        }
        return $data;
    }

    /**
     * Добавлять характеристику активной.
     * @return boolean
     */
    public function isActiveCreateVocabulary() {
        return $this->is_active_create_vocabulary;
    }

    /**
     * Добавлять значение характеристики - активной.
     * @param boolean $is_active_create_vocabulary
     * @return $this
     */
    public function setIsActiveCreateVocabulary($is_active_create_vocabulary = true) {
        $this->is_active_create_vocabulary = $is_active_create_vocabulary;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActiveCreateTerm() {
        return $this->is_active_create_term;
    }

    /**
     * @param boolean $is_active_create_term
     * @return $this
     */
    public function setIsActiveCreateTerm($is_active_create_term = true) {
        $this->is_active_create_term = $is_active_create_term;
        return $this;
    }

    /**
     * Очистка кеша характеристик товара.
     * @return $this
     */
    public function clearCache() {
        $id = $this->getProductModel()->id;
        \Yii::$app->cache->set(self::CACHE_PRODUCT_VOCABULARY_TERM_DATA. $id, null);
        return $this;
    }
}