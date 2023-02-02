<?php

namespace common\models;

use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\libs\sphinx\QueryBuilder;
use common\models\goods\Product;
use common\libs\Transliterate;

/**
 * This is the model class for table "vocabulary".
 *
 * @property integer $id
 * @property string $title
 * @property string $alias
 * @property integer $type
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class Vocabulary extends Base {

    # типы возможных значений характеристики.
    const TYPE_VALUE = 1; // тип "значение" - может быть float/int число.
    const TYPE_RANGE = 5; // тип "Диапазон с двумя точками" - пользователь выбирает 2 точки "от" и "до" в диапазоне значений.
    const TYPE_SELECT = 3; // тип "выбор предопределённых значений" может быть несколько значений.
    const TYPE_TEXT = 4; // произвольное значение.

    public static function tableName() {
        return 'vocabulary';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return array
     */
    public static function getTypes(): array {
        return [
            self::TYPE_VALUE        => 'Значение',
            self::TYPE_RANGE        => 'Диапазон',
            self::TYPE_SELECT       => 'Выбор значения',
            self::TYPE_TEXT         => 'Текст',
        ];
    }

    /**
     * @return int
     */
    public function getType(): int {
        return (int)$this->type;
    }

    /**
     * Возвращает название типа.
     * @param null|integer $type
     * @return null|string
     */
    public function getTypeName($type = null): ?string {
        $type = (null !== $type) ? $type : $this->getType();
        return static::getTypes()[$type] ?? null;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert) {
        $this->alias = strtolower(Transliterate::i()->get($this->title));
        $this->alias = str_replace(' ', '_', $this->alias);
        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
    public function isRangeType(): bool {
        return (int)$this->type === self::TYPE_RANGE;
    }

    /**
     * @return bool
     */
    public function isSelectType(): bool {
        return (int)$this->type === self::TYPE_SELECT;
    }

    /**
     * Проверяет, может ли характеристика хранить несколько значений.
     * @param int|null $type
     * @return boolean
     */
    public function isMultipleValueType($type = null): bool {
        $type = null === $type && !$this->isNewRecord ? $this->getType() : $type;
        switch ($type) {
            case self::TYPE_VALUE:
                return false;
            case self::TYPE_SELECT:
                return true;
            case self::TYPE_TEXT:
                return false;
            case self::TYPE_RANGE:
                return true;
            default:
                return false;
        }
    }

    /**
     * Метод определяет в каком столбце лежит значение характеристики из связной таблицы `product_vocabulary_option`.
     * Значение может храниться как ссылка на vocabulary_options (option_id) либо в product_vocabulary_option.value
     * @param int|null $type
     * @return boolean
     */
    public function isOptionIdValue($type = null) {
        $type = null === $type && !$this->isNewRecord ? $this->getType() : $type;
        switch ($type) {
            case self::TYPE_VALUE:
                return false;
            case self::TYPE_SELECT:
                return true;
            case self::TYPE_TEXT:
                return false;
            case self::TYPE_RANGE:
                return false;
            default:
                return false;
        }
    }

    /**
     * Возвращает предопределённые значения характеристики.
     * @param int $category_id
     * @param bool $with_nested
     * @return ActiveQuery
     */
    public function getVocabularyOptions(int $category_id, bool $with_nested = true): ActiveQuery {
        $query = $this->hasMany(VocabularyOption::class, ['vocabulary_id' => 'id'])
            ->where(['category_id' => $category_id]);
        if ($with_nested) {
            /** @var Category|null $category */
            $category = Category::findOne($category_id);
            if (null !== $category) {
                $categories = array_merge(
                    $category->getAllParentIds(),
                    $category->getAllChildIds(),
                    [$category_id]
                );
                $categories = array_unique($categories);
                $query->where(['category_id' => $categories]);
            }
        }
        return $query;
    }

    /**
     * Возможный список характеристик для категории.
     * Используется для добавления характеристики к категории (выбор характеристики).
     * todo: возможно нужо будет добавить кеш.
     * @param integer $category_id
     * @return array
     */
    public function getPossibleVocabulariesForCategory(int $category_id): array {

        // уже привязанные характеристики.
        $category_vocabularies = CategoryVocabulary::find()
            ->select(['vocabulary_id'])
            ->where(['category_id' => $category_id])
            ->asArray()
            ->all();
        $category_vocabularies = ArrayHelper::getColumn($category_vocabularies, 'vocabulary_id');

        // характеристики вверх / вниз по дереву категорий.
        /** @var Category $category */
        $category = Category::findOne($category_id);
        $parent_ids = $category->getAllParentIds();
        $child_ids = $category->getAllChildIds();

        $parent_category_vocabularies = CategoryVocabulary::find()
            ->select(['vocabulary_id'])
            ->where(['category_id' => array_unique(array_merge($parent_ids, $child_ids))])
            ->asArray()
            ->all();
        $parent_category_vocabularies = ArrayHelper::getColumn($parent_category_vocabularies, 'vocabulary_id');

        $not_in = array_merge($parent_category_vocabularies, $category_vocabularies);
        $not_in = array_unique($not_in);

        return Vocabulary::find()
            ->select(['id', 'title', 'type'])
            ->where(['NOT IN', 'id', $not_in])
            ->asArray()
            ->all();
    }

    /**
     * Обновляет / добавляет опции характеристике.
     * @param int $category_id
     * @param array $options
     * @throws Exception
     * @return bool
     */
    public function updateOptions(int $category_id, array $options): bool {
        if (empty($options) || !$category_id) {
            return true;
        }
        if ($this->isNewRecord) {
            return true;
        }
        /** @var Category $category */
        $category = Category::findOne($category_id);
        if (null === $category) {
            throw new Exception('Category not found');
        }
        $this->removeOptionsByCategory($category_id);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($options as $option) {
            $vocabulary_option = new VocabularyOption();
            $vocabulary_option->setAttributes([
                'category_id' => $category_id,
                'vocabulary_id' => $this->id,
                'value' => (string)$option,
            ]);
            $result = $vocabulary_option->save();
            if (!$result) {
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return true;
    }

    /**
     * @param int $category_id
     * @return bool
     */
    public function removeOptionsByCategory(int $category_id): bool {
        if ($this->isNewRecord) {
            return true;
        }
        VocabularyOption::deleteAll([
            'vocabulary_id' => $this->id,
            'category_id' => $category_id,
        ]);
        return true;
    }

    /**
     * @return bool
     */
    public function removeOptions(): bool {
        if ($this->isNewRecord) {
            return true;
        }
        VocabularyOption::deleteAll(['vocabulary_id' => $this->id]);
        return true;
    }

    /**
     * @param int $category_id
     * @param bool $with_parents
     * @return Vocabulary[]
     */
    public static function getVocabularyListByCategory($category_id, $with_parents = false) {
        return static::getVocabularyListQueryByCategory($category_id, $with_parents)->all();
    }

    /**
     * Возвращает ActiveQuery характеристик по категории.
     * @param int $category_id
     * @param bool $with_parents - подгружать характеристики родительских категорий.
     * @return ActiveQuery
     */
    public static function getVocabularyListQueryByCategory($category_id, $with_parents = false) {
        $vocabulary_ids = static::getVocabularyIdsByCategoryId($category_id, $with_parents);
        return static::find()
            ->where(['id' => $vocabulary_ids]);
    }

    /**
     * Возвращает список id характеристик по категории.
     * TODO: закешировать.
     * @param int $category_id
     * @param bool $with_parents
     * @return array
     */
    public static function getVocabularyIdsByCategoryId($category_id, $with_parents = false) {
        /** @var Category $category */
        $category = Category::findOne((int)$category_id);
        if (null === $category) {
            return [];
        }
        $category_id_list = [$category->id];
        if ($with_parents) {
            $category_id_list = array_merge($category_id_list, $category->getAllParentIds());
        }
        $vocabulary_ids = CategoryVocabulary::find()
            ->select(['vocabulary_id'])
            ->where(['IN', 'category_id', $category_id_list])
            ->asArray()
            ->all();
        return ArrayHelper::getColumn($vocabulary_ids, 'vocabulary_id');
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['title'], 'trim'],
            [['title'], 'unique'],
            [['created_at', 'updated_at', 'type',], 'integer'],
            [['alias', 'title'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'id'            => '#',
            'title'         => 'Название характеристики',
            'type'          => 'Тип характеристики',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params) {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    /**
     * Обновление записи в индексе.
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->updateSphinxIndex();
    }

    /**
     * Возвращает ActiveQuery товаров с этой характеристикой.
     * @return ActiveQuery
     */
    public function getProducts() {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable(VocabularyOption::tableName(), ['vocabulary_id' => 'id']);
    }

    /**
     * Возвращает список предустановленных значений характеристики.
     * @param int $category_id
     * @return array
     */
    public function getOptionsArray(int $category_id) {
        if ($this->isNewRecord) {
            return [];
        }
        // todo: закешировать...
        $options = $this->getVocabularyOptions($category_id)
            ->select(['id', 'value as title'])
            ->asArray()
            ->all();
        return ArrayHelper::map($options, 'id', 'title');
    }

    /**
     * Используется ли характеристика в товарах.
     * @return bool
     */
    public function useInProduct() {
        $count_relation_product = VocabularyOption::find()
            ->where(['vocabulary_id' => $this->id])
            ->count();
        return intval($count_relation_product) > 0;
    }

    public function beforeDelete() {
        /** используется в товарах. */
        if ($this->useInProduct()) {
            return false;
        }
        return parent::beforeDelete();
    }

    public function afterDelete() {
        parent::afterDelete();
        // удаление значений характеристики.
        $this->removeOptions();
        // удаление из sphinx.
        $this->removeSphinxIndex();
    }

    /**
     * @return string
     */
    public function getTitle() {
        return (string)$this->title;
    }

    /**
     * Обновление характеристики в индексе.
     * @return bool|int
     */
    public function updateSphinxIndex() {
        ini_set('memory_limit', '2048M');
        $row = [
            'id' => $this->id,
            'title' => (string)$this->title,
            'type' => (int)$this->type,
        ];
        $builder = new QueryBuilder(\Yii::$app->get('sphinx'));
        return $builder->sphinxSave(self::tableName(), $this->id, $row);
    }
}