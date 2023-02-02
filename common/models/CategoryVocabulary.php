<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

use common\libs\cache\TagsCache;

use backend\components\CatalogProcessor;

/**
 * This is the model class for table "category_vocabularies".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $vocabulary_id
 * @property integer $unit_code
 * @property integer $use_in_product_name
 * @property integer $use_only_vocabulary_name
 * @property integer $use_only_vocabulary_value
 * @property float $range_from
 * @property float $range_to
 * @property float $range_step
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class CategoryVocabulary extends Base {

    /** @see \common\models\CategoryVocabulary::getVocabulariesByCategory */
    const TAG_VOCABULARIES_BY_CATEGORY = 'tag_vocabularies_by_category';
    const CACHE_VOCABULARIES_BY_CATEGORY = 'cache_vocabularies_by_category_';
    /** @see \common\models\CategoryVocabulary::getVocabularyDataByCategory */
    const CACHE_VOCABULARY_DATA_BY_CATEGORY = 'cache_vocabulary_data_by_category';

    public static function tableName() {
        return 'category_vocabularies';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Возвращает данные по связи характеристики с категорией.
     * @param int $category_id
     * @param bool $with_nested - отдавать данные характеристик родительских категорий ? (использовать наследование.)
     * @return array
     */
    public static function getVocabularyDataByCategory(int $category_id, bool $with_nested = true) {
        /** @var Category $category */
        $category = Category::findOne($category_id);
        if (null === $category) {
            return [];
        }
        $cache_key = self::getVocabularyDataByCategoryCacheKey($category_id, $with_nested);
        if (!$vocabulary_list = TagsCache::get($cache_key)) {

            $category_id_list = [$category_id];
            if ($with_nested) {
                $category_id_list = array_merge($category_id_list, $category->getAllParentIds($category_id));
                $category_id_list = array_unique($category_id_list);
            }
            $vocabulary_list = static::find()
                ->alias('cv')
                ->select([
                    'cvp.position', 'v.id', 'cv.category_id', 'cv.vocabulary_id', 'cv.unit_code',
                    'cv.use_in_product_name', 'cv.use_only_vocabulary_name', 'cv.use_only_vocabulary_value',
                    'v.alias', 'v.title', 'v.type', 'cv.range_from', 'cv.range_to', 'cv.range_step',
                ])
                ->innerJoin(Vocabulary::tableName(). ' as v', 'v.id = cv.vocabulary_id')
                ->leftJoin(CategoryVocabularyPosition::tableName().' as cvp', 'cvp.category_id=:category_id and cvp.vocabulary_id=cv.vocabulary_id', [
                    ':category_id' => $category_id
                ])
                ->where(['cv.category_id' => $category_id_list])
                ->orderBy('cvp.position ASC')
                ->asArray()
                ->all();

            TagsCache::set(self::TAG_VOCABULARIES_BY_CATEGORY, $cache_key, $vocabulary_list);
        }
        return $vocabulary_list;
    }

    /**
     * Возвращает характеристики по категории.
     * @param int $category_id
     * @return Vocabulary[]
     */
    public static function getVocabulariesByCategory($category_id) {
        $category_id = (int)$category_id;
        $cache_key = self::getVocabulariesByCategoryCacheKey($category_id);
        if (!$vocabulary_list = TagsCache::get($cache_key)) {
            $vocabulary_id_list = static::find()
                ->select(['vocabulary_id'])
                ->where(['category_id' => $category_id])
                ->asArray()
                ->all();
            $vocabulary_id_list = ArrayHelper::getColumn($vocabulary_id_list, 'vocabulary_id');
            $vocabulary_list = Vocabulary::find()
                ->where(['id' => $vocabulary_id_list])
                ->all();
            TagsCache::set(self::TAG_VOCABULARIES_BY_CATEGORY, $cache_key, $vocabulary_list);
        }
        return $vocabulary_list;
    }

    /**
     * Удаление связи категории с характеристикой.
     * @param int $category_id
     * @param int $vocabulary_id
     * @return bool
     */
    public static function removeRelation(int $category_id, int $vocabulary_id): bool {
        /** @var Vocabulary $vocabulary */
        $vocabulary = Vocabulary::findOne($vocabulary_id);
        if (null === $vocabulary) {
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        $result = static::deleteAll(['category_id' => $category_id, 'vocabulary_id' => $vocabulary_id]) > 0;
        if (!$result) {
            $transaction->rollBack();
            return false;
        }
        $result = $vocabulary->removeOptionsByCategory($category_id);
        if (!$result) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        static::clearCache();
        CatalogProcessor::clearCache();
        return true;
    }

    /**
     * Обновление свойств связи. (Использовать в названии товара, использовать значение характеристики.)
     * @param int $category_id
     * @param int $vocabulary_id
     * @param string $property
     * @param bool $flag
     * @return bool
     */
    public static function updatePropertyFlags(int $category_id, int $vocabulary_id, string $property, bool $flag = true): bool {
        if (!in_array($property, [
                'use_in_product_name',
                'use_only_vocabulary_name',
                'use_only_vocabulary_value'])) {
            return false;
        }
        $relation = static::find()->where([
            'category_id' => $category_id,
            'vocabulary_id' => $vocabulary_id,
        ])->one();
        if (null === $relation) {
            return false;
        }
        $relation->{$property} = (int)$flag;
        $result = $relation->save();
        if ($result) {
            CategoryVocabulary::clearCache();
        }
        return $result;
    }

    /**
     * @param int $category_id
     * @return string
     */
    private static function getVocabulariesByCategoryCacheKey($category_id) {
        return self::CACHE_VOCABULARIES_BY_CATEGORY. (int)$category_id;
    }

    /**
     * @param int $category_id
     * @param bool $with_nested
     * @return string
     */
    private static function getVocabularyDataByCategoryCacheKey(int $category_id, bool $with_nested = true) {
        return self::CACHE_VOCABULARY_DATA_BY_CATEGORY. $category_id. '_'. (int)$with_nested;
    }

    /**
     * @param float $range_from
     * @return $this
     */
    public function setRangeFrom(float $range_from) {
        $this->range_from = $range_from;
        return $this;
    }

    /**
     * @param float $range_to
     * @return $this
     */
    public function setRangeTo(float $range_to) {
        $this->range_to = $range_to;
        return $this;
    }

    /**
     * @param float $range_step
     * @return $this
     */
    public function setRangeStep(float $range_step) {
        $this->range_step = $range_step;
        return $this;
    }

    /**
     * @return float
     */
    public function getRangeFrom() {
        return (float)$this->range_from;
    }

    /**
     * @return float
     */
    public function getRangeTo() {
        return (float)$this->range_to;
    }

    /**
     * @return float
     */
    public function getRangeStep() {
        return (float)$this->range_step;
    }

    public function rules() {
        return [
            [['category_id', 'vocabulary_id'], 'required'],
            [['category_id', 'vocabulary_id', 'unit_code', 'created_at', 'updated_at', 'use_in_product_name', 'use_only_vocabulary_name', 'use_only_vocabulary_value',], 'integer'],
            [['vocabulary_id'], 'unique', 'targetAttribute' => ['category_id', 'vocabulary_id'],
                'message' => 'К данной категории уже привязанна эта характеристика'],
            [['range_from', 'range_to', 'range_step'], 'number'],
        ];
    }

    public static function clearCache() {
        TagsCache::clearCacheByTag(self::TAG_VOCABULARIES_BY_CATEGORY);
    }

    public function afterSave($insert, $changedAttributes) {
        static::clearCache();
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete() {
        static::clearCache();
        parent::afterDelete();
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'category_id' => 'Идентификатор категории',
            'vocabulary_id' => 'Идентификатор характеристики',
            'unit_code' => 'Код единицы измерения',
            'use_in_product_name' => 'Использовать в названии товара',
            'use_only_vocabulary_value' => 'Использовать значение в названии товара',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}