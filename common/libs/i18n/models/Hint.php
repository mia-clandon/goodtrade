<?php

namespace common\libs\i18n\models;

use common\models\Base;

use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "source_message".
 *
 * @property int $id
 * @property string $category
 * @property string $message
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Message[] $messages
 * @author Артём Широких kowapssupport@gmail.com
 */
class Hint extends Base {

    const CACHE_KEY_HINT_BY_ID = 'hint_by_id_';

    public static function tableName() {
        return 'translate_source_message';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['message', 'category',], 'string'],
            [['message', 'user_id', 'category',], 'required'],
            [['category',], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'user_id',], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'id'        => '#',
            'category'  => 'Категория',
            'message'   => 'Исходный текст',
            'user_id'   => 'Кто добавил',
        ];
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->user_id = \Yii::$app->getUser()->id;
            // todo: обработать текст.
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider {
        $query = self::find();
        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $data_provider;
        }
        return $data_provider;
    }

    /**
     * @param int $id
     * @return Hint|null
     */
    public static function getHintById(int $id): ?self {
        $cache_key = self::CACHE_KEY_HINT_BY_ID. $id;
        if (!$hint_model = \Yii::$app->cache->get($cache_key)) {
            /** @var self $hint_model */
            $hint_model = self::findOne($id);
            if (null === $hint_model) {
                return null;
            }
            \Yii::$app->cache->set($cache_key, $hint_model);
        }
        return $hint_model;
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getSourceText(int $id): string {
        $hint = static::getHintById($id);
        if (null === $hint) {
            return '';
        }
        return $hint->message;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages(): ActiveQuery {
        return $this->hasMany(Message::class, ['id' => 'id']);
    }

    /**
     * @param string $language_code
     * @return string
     */
    public function getTranslate(string $language_code): string {

    }
}