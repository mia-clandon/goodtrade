<?php

namespace common\models;

use common\models\goods\Product;

use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Модель для работы предустановленных значений характеристик.
 * Тип: Vocabulary::TYPE_TEXT, Vocabulary::TYPE_SELECT ...
 * @property int $category_id
 * @property int $vocabulary_id
 * @property string $value
 * @property int $created_at
 * @property int $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class VocabularyOption extends Base {

    const TABLE_NAME = 'vocabulary_options';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['value', 'vocabulary_id', 'category_id',], 'required'],
            [['created_at', 'updated_at', 'vocabulary_id', 'category_id',], 'integer'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params) {
        $query = self::find()->where($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function attributeLabels() {
        return [
            'id'            => '#',
            'vocabulary_id' => 'Характеристика',
            'category_id'   => 'Категория',
            'value'         => 'Значение характеристики',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProductListQuery() {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable(VocabularyOption::tableName(), ['option_id' => 'id']);
    }

    /**
     * @return boolean
     */
    public function useInProduct() {
        return $this->getProductListQuery()->count() > 0;
    }

    public function beforeDelete() {
        /** используется ли в товарах. */
        if ($this->useInProduct()) {
            return false;
        }
        return parent::beforeDelete();
    }

    public function afterDelete() {
        $vocabulary = Vocabulary::findOne($this->vocabulary_id);
        if ($vocabulary) {
            $vocabulary->updateSphinxIndex();
        }
        return parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes) {
        $vocabulary = Vocabulary::findOne($this->vocabulary_id);
        if ($vocabulary) {
            $vocabulary->updateSphinxIndex();
        }
        parent::afterSave($insert, $changedAttributes);
    }
}