<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * Class CategoryVocabularyPosition
 * @property int $category_id
 * @property int $vocabulary_id
 * @property int $position
 * @package common\models
 * @author Артём Широких kowapssupport@gmail.com
 */
class CategoryVocabularyPosition extends Base {

    public static function tableName() {
        return 'category_vocabulary_positions';
    }

    public function rules() {
        return [
            [['category_id', 'vocabulary_id'], 'required'],
            [['category_id', 'vocabulary_id', 'position'], 'number'],
        ];
    }

    /**
     * Обновление позиций характеристик категории.
     * @param array $positions_data
     * @return bool
     */
    public static function updatePositions(array $positions_data): bool {
        if (empty($positions_data)) {
            return false;
        }
        $category_id_column = ArrayHelper::getColumn($positions_data, 'category_id');
        if (count(array_unique($category_id_column)) > 1) {
            return false;
        }
        $category_id = ArrayHelper::getValue($category_id_column, 0);
        if (null === $category_id) {
            return false;
        }
        static::deleteAll(['category_id' => (int)$category_id]);
        $rows = [];
        foreach ($positions_data as $position => $data) {
            $category_id = $data['category_id'] ?? 0;
            $vocabulary_id = $data['vocabulary_id'] ?? 0;
            if (!$category_id || !$vocabulary_id) {
                continue;
            }
            $rows[] = [
                'category_id' => $category_id,
                'vocabulary_id' => $vocabulary_id,
                'position' => $position,
            ];
        }
        \Yii::$app->db->createCommand()
            ->batchInsert(static::tableName(), ['category_id', 'vocabulary_id', 'position'], $rows)
            ->execute();
        CategoryVocabulary::clearCache();
        return true;
    }

    public static function removePosition(int $category_id, int $vocabulary_id): void {
        static::deleteAll(['category_id' => $category_id, 'vocabulary_id' => $vocabulary_id]);
    }
}