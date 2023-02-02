<?php

namespace common\models\goods\helpers;

use common\models\CategoryVocabulary;
use common\models\Unit;
use common\models\VocabularyOption;
use common\models\goods\VocabularyOption as ProductVocabularyOption;
use common\models\Vocabulary as VocabularyModel;

use backend\components\form\controls\Range;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Хелпер для работы с характеристиками товара.
 * Class Vocabulary
 * @author Артём Широких kowapssupport@gmail.com
 */
class Vocabulary extends Base {

    /** @see \common\models\goods\helpers\Vocabulary::getValues */
    const PRODUCT_VOCABULARY_VALUES_CACHE = 'product_vocabulary_cache_';
    /** @see \common\models\goods\helpers\Vocabulary::getOptionIds */
    const PRODUCT_OPTION_ID_CACHE = 'product_option_id_cache_';

    const VOCABULARY_ID_KEY = 'vocabulary_id';
    const OPTION_ID_KEY = 'option_id';
    const VALUE_KEY = 'value';

    const KEY_VALUE = 'value';
    const KEY_ID = 'id';
    const KEY_VOCABULARY = 'vocabulary';
    const KEY_VOCABULARY_VALUE = 'vocabulary_value';

    /** @var array */
    private $product_vocabulary_data = [];

    /**
     * Возвращает возможные характеристики для товара.
     * @return \common\models\Vocabulary[]
     */
    public function getPossibleVocabularyList() {
        $model = $this->getProductModel();
        if ($model->isNewRecord) {
            return [];
        }
        return VocabularyModel::getVocabularyListByCategory($model->getCategoryId(), true);
    }

    /**
     * Устанавливает данные характеристик с формы товара.
     * @param array $vocabulary_data
     * @return $this
     */
    public function setProductVocabularyData(array $vocabulary_data) {
        $this->product_vocabulary_data = $vocabulary_data;
        return $this;
    }

    /**
     * Метод возвращает id выбранных опций характеристик товара.
     * @return array
     */
    public function getOptionIds() {
        if ($this->getProductModel()->isNewRecord) {
            return [];
        }
        $cache_key = $this->getProductOptionIdCacheKey($this->getProductModel()->id);
        $option_id_list = \Yii::$app->cache->get($cache_key);
        if (false === $option_id_list) {
            $option_id_list = ProductVocabularyOption::find()
                ->alias('pvo')
                ->select(['pvo.option_id'])
                ->where(['pvo.product_id' => $this->getProductModel()->id,])
                ->andWhere('pvo.option_id IS NOT NULL')
                ->asArray()
                ->all();
            $option_id_list = ArrayHelper::getColumn($option_id_list, 'option_id', []);
            $option_id_list = array_map('intval', $option_id_list);
            \Yii::$app->cache->set($cache_key, $option_id_list);
        }
        return $option_id_list;
    }

    /**
     * Возвращает значения всех характеристик товара.
     * @return array
     */
    public function getValues() {
        if ($this->getProductModel()->isNewRecord) {
            return [];
        }
        $cache_key = $this->getProductVocabularyValuesCacheKey($this->getProductModel()->id);
        $values = \Yii::$app->cache->get($cache_key);

        if (false === $values) {
            $vocabulary_model = new VocabularyModel();
            $rows = ProductVocabularyOption::find()
                ->alias('pvo')
                ->select([
                    'pvo.option_id',
                    new Expression('IF (pvo.value IS NULL, vo.value, pvo.value) as value'),
                    'pvo.vocabulary_id',
                    'v.type',
                ])
                ->where(['pvo.product_id' => $this->getProductModel()->id])
                ->innerJoin(VocabularyModel::tableName().' as v', 'v.id = pvo.vocabulary_id')
                ->leftJoin(VocabularyOption::tableName().' as vo', 'pvo.option_id = vo.id')
                ->asArray()
                ->all();

            $values = [];
            /** @var array $row */
            foreach ($rows as $row) {
                // значения диапазона.
                if ((int)$row['type'] === VocabularyModel::TYPE_RANGE) {
                    $values[(int)$row[self::VOCABULARY_ID_KEY]][] = $row[self::VALUE_KEY];
                }
                // multiple значения (из предустановленных значений характеристик).
                elseif ($vocabulary_model->isMultipleValueType($row['type'])) {
                    $values[(int)$row[self::VOCABULARY_ID_KEY]][$row[self::OPTION_ID_KEY]] = $row[self::VALUE_KEY];
                }
                // одиночное значение.
                else {
                    $values[(int)$row[self::VOCABULARY_ID_KEY]] = $row[self::VALUE_KEY];
                }
            }
            \Yii::$app->cache->set($cache_key, $values);
        }
        return $values;
    }

    /**
     * @param int $product_id
     * @return string
     */
    private function getProductVocabularyValuesCacheKey($product_id) {
        return self::PRODUCT_VOCABULARY_VALUES_CACHE. (int)$product_id;
    }

    /**
     * @param int $product_id
     * @return string
     */
    private function getProductOptionIdCacheKey($product_id) {
        return self::PRODUCT_OPTION_ID_CACHE. (int)$product_id;
    }

    /**
     * @param int $product_id
     * @return $this
     */
    private function clearCache($product_id) {
        \Yii::$app->cache->delete($this->getProductVocabularyValuesCacheKey($product_id));
        \Yii::$app->cache->delete($this->getProductOptionIdCacheKey($product_id));
        return $this;
    }

    /**
     * Подготавливает данные по характеристикам из формы к сохранению.
     * @return array
     */
    private function getPreparedValues() {
        $product_vocabulary_data = [];

        foreach ($this->product_vocabulary_data as $vocabulary_id => $value) {
            if (null === $value || empty($value)) {
                continue;
            }
            /** @var VocabularyModel|null $vocabulary */
            $vocabulary = VocabularyModel::findOne($vocabulary_id);
            if (null === $vocabulary) {
                continue;
            }
            if (is_array($value) && $vocabulary->isRangeType()) {
                if ((!isset($value[Range::RANGE_FROM_PROPERTY])
                    || !isset($value[Range::RANGE_TO_PROPERTY]))) {
                    continue;
                }
                // 2 значения одиночного диапазона.
                if ($value[Range::RANGE_FROM_PROPERTY] === $value[Range::RANGE_TO_PROPERTY]) {
                    unset($value[Range::RANGE_TO_PROPERTY]);
                }
            }
            // проверка значения.
            $product_vocabulary_data[$vocabulary_id] = [
                'value' => $value,
                'is_option_id_value' => $vocabulary->isOptionIdValue(),
            ];
        }
        return $product_vocabulary_data;
    }

    /**
     * Обновление значений характеристик товара.
     * @return bool
     */
    public function updateVocabularyValues() {
        $form_values = $this->getPreparedValues();

        // проверяю, поменялись ли значения характеристик, нужно ли сохранять.
        if (!$this->getProductModel()->isNewRecord && !$this->isChangeValues()) {
            return true;
        }
        if (empty( $form_values)) {
            return true;
        }

        $product_id = $this->getProductModel()->id;
        if (!$this->getProductModel()->isNewRecord) {
            $this->clearVocabularyValues();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($form_values as $vocabulary_id => $data) {
            $is_option_id_value = (bool)ArrayHelper::getValue($data, 'is_option_id_value', false);

            $product_vocabulary_option = new ProductVocabularyOption();
            $product_vocabulary_option->vocabulary_id = (int)$vocabulary_id;
            $product_vocabulary_option->product_id = (int)$product_id;

            $value_property = ($is_option_id_value) ? "option_id" : "value";
            $value = ArrayHelper::getValue($data, 'value');
            $values = !is_array($value) ? [$value] : $value;

            foreach ($values as $value) {

                $product_vocabulary_option->{$value_property} = $value;
                $product_vocabulary_option->id = null;
                $product_vocabulary_option->setIsNewRecord(true);
                $result = $product_vocabulary_option->save();

                if (!$result) {
                    $transaction->rollBack();
                    return false;
                }
            }
        }
        $transaction->commit();
        return true;
    }

    /**
     * Метод проверяет, изменились ли значения характеристик.
     * @return boolean
     */
    public function isChangeValues() {
        if ($this->getProductModel()->isNewRecord) {
            return false;
        }
        $form_values = $this->getPreparedValues();
        $db_values = $this->getValues();

        if (count($form_values) !== count($db_values)) {
            return true;
        }
        // привожу значения из db к структуре значений из формы;
        $new_db_values = [];
        foreach ($db_values as $vocabulary_id => $value) {
            // массив значений (предопределённые значения);
            if (is_array($value)) {
                $new_value_array = [];
                foreach ($value as $option_id => $item) {
                    $new_value_array['value'][] = (string)$option_id;
                    $new_value_array['is_option_id_value'] = $form_values[$vocabulary_id]['is_option_id_value'];
                }
                $new_db_values[$vocabulary_id] = $new_value_array;
            }
            else {
                $new_db_values[$vocabulary_id] = [
                    'value' => $value,
                    'is_option_id_value' => $form_values[$vocabulary_id]['is_option_id_value'],
                ];
            }
        }
        ksort($form_values);
        ksort($new_db_values);
        return md5(json_encode($form_values)) !== md5(json_encode($new_db_values));
    }

    /**
     * Очищает значения характеристик товара.
     * @return $this
     */
    public function clearVocabularyValues() {
        $product_model = $this->getProductModel();
        if ($product_model->isNewRecord) {
            return $this;
        }
        ProductVocabularyOption::deleteAll([
            'product_id' => $product_model->id,
        ]);
        $this->clearCache($product_model->id);
        return $this;
    }

    /**
     * todo: вынести потом в нужный декоратор.
     * @return array
     */
    public function getDecoratedValues(): array {
        $product = $this->getProductModel();
        $vocabulary_data = CategoryVocabulary::getVocabularyDataByCategory($product->getCategoryId());
        $vocabulary_values = $product->getVocabularyHelper()->getValues();

        $decorated_values = [];
        foreach ($vocabulary_data as $vocabulary_item) {
            $type = (int)$vocabulary_item['type'];
            $unit_text = '';
            $unit = Unit::findByCodeCached((int)$vocabulary_item['unit_code'] ?? 0);
            if (null !== $unit) {
                $unit_text = ', ' . $unit->symbol_national;
            }
            $value = '';
            $value_info = $vocabulary_values[$vocabulary_item['id']] ?? null;

            if ($value_info !== null) {
                if ($type === VocabularyModel::TYPE_RANGE) {
                    $from = min($value_info);
                    $to = max($value_info);
                    $value = ($from === $to) ? $from : $from . ' - ' . $to;
                }
                if ($type === VocabularyModel::TYPE_SELECT) {
                    $value = implode(', ', $value_info);
                }
                if (is_string($value_info)) {
                    $value = $value_info;
                }
            }
            $decorated_values[] = [
                self::KEY_VOCABULARY => [
                    self::KEY_ID => $vocabulary_item['id'],
                    self::KEY_VALUE => $vocabulary_item['title']. $unit_text,
                ],
                self::KEY_VOCABULARY_VALUE => [
                    self::KEY_VALUE => $value,
                ],
            ];
        }
        return $decorated_values;
    }
}