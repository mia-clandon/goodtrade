<?php

namespace common\models\seo;

use common\models\Base;

/**
 * This is the model class for table "seo_meta".
 *
 * @property integer $id
 * @property string $route
 * @property string $params
 * @property string $title
 * @property string $metakeys
 * @property string $metadesc
 * @property string $tags
 * @property integer $robots
 * @author Артём Широких kowapssupport@gmail.com
 */
class Meta extends Base {

    const TABLE_NAME = 'seo_meta';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['tags'], 'string'],
            [['robots'], 'integer'],
            [['route', 'params', 'title', 'metakeys', 'metadesc'], 'string', 'max' => 255],
            [['route'], 'unique'],
            [['params'], 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'route' => 'Route',
            'params' => 'Params',
            'title' => 'Title',
            'metakeys' => 'Metakeys',
            'metadesc' => 'Metadesc',
            'tags' => 'Tags',
            'robots' => 'Robots',
        ];
    }
}