<?php

namespace common\models\goods;

use common\models\Base;

/**
 * This is the model class for table "product_categories".
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property int firm_id
 * @author Артём Широких kowapssupport@gmail.com
 */
class Categories extends Base {

    const TABLE_NAME = 'product_categories';

    /** @var int|null */
    public $product_count;

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['product_id', 'category_id',], 'required'],
            [['product_id', 'category_id', 'firm_id'], 'integer'],
            [['product_id', 'category_id'], 'unique', 'targetAttribute' => ['product_id', 'category_id'], 'message' => 'The combination of Product ID and Category ID has already been taken.'],
        ];
    }

    public function attributeLabels() {
        return [
            'id'            => '#',
            'product_id'    => 'Product ID',
            'category_id'   => 'Category ID',
            'firm_id'       => 'Firm ID',
        ];
    }

    /**
     * Возвращает количество товаров по категориям.
     * @param array $category_ids
     * @return int
     */
    public function getProductCountByCategories(array $category_ids): int {
        return Categories::find()->where(['category_id' => $category_ids])->count();
    }
}