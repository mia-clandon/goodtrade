<?php

namespace common\models;

/**
 * This is the model class for table "category_relation".
 *
 * @property integer $category_id
 * @property integer $related_category_id
 * @property integer $type
 * @author yerganat
 */
class CategoryRelation extends Base {

    /** Тип связи - дубликат. */
    const TYPE_RELATION_DUPLICATE = 1;
    /** Тип связи - ссылка. */
    const TYPE_RELATION_LINK = 2;

    public static function tableName() {
        return 'category_relation';
    }

    public function rules() {
        return [
            [['category_id', 'related_category_id', 'type'], 'integer'],
            [['category_id', 'related_category_id'], 'unique', 'targetAttribute' => ['category_id', 'related_category_id'], 'message' => 'The combination of # категории and № связанная категория has already been taken.'],
        ];
    }

    public function attributeLabels() {
        return [
            'category_id' => '# категория',
            'related_category_id' => '# связанной категории',
            'type' => 'Тип: связь, дубликат',
        ];
    }
}