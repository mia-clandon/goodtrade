<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "category_slider".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $image
 * @property string $tag
 * @property string $title
 * @property string $link
 * @property integer $firm_id
 * @property string $description
 * @property integer $created_at
 * @property integer $update_at
 *
 * @author yerganat
 */
class CategorySlider extends Base {

    //новости
    const TAG_NEWS = 'news';
    //новый поставщик
    const TAG_NEW_FIRM = 'new_firm';
    //обновление
    const TAG_UPDATE = 'update';

    public static function tableName() {
        return 'category_slider';
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function beforeSave($insert) {
        if ($saved = parent::beforeSave($insert)) {
            $this->uploadImage('category_slider');
        }
        return $saved;
    }

    public function beforeDelete() {
        $this->clearImage();
        return parent::beforeDelete();
    }

    public function rules() {
        return [
            [['category_id', 'tag', 'title'], 'required'],
            [['category_id', 'created_at', 'firm_id'], 'integer'],
            [['tag', 'title', 'image'], 'string', 'max' => 255],
            [['link', 'description'], 'string'],
            [['firm_id'], 'checkIfFirmTag'],
        ];
    }

    /**
     * Валидирует поле организации в случае если выбран тип TAG_NEW_FIRM.
     * @param string $attribute
     * @return bool
     */
    public function checkIfFirmTag($attribute): bool {
        $firm_id = $this->getAttribute($attribute);
        if($this->tag === self::TAG_NEW_FIRM && empty($firm_id)) {
            $this->addError($attribute, 'Поле "компания" обязательно для выбора.');
            return false;
        }
        return true;
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'category_id' => 'Категория',
            'image'  => 'Фото слайда',
            'tag' => 'Тэг',
            'title' => 'Заголовок',
            'link' => 'Ссылка',
            'description' => 'Текст',
            'created_at'    => 'Время создания',
            'updated_at'    => 'Время обновления',
        ];
    }

    /**
     * @param $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params) {
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
     * @return array
     */
    public function geTags() {
        return [
            self::TAG_NEWS => 'Новости',
            self::TAG_NEW_FIRM => 'Новый поставщик',
            self::TAG_UPDATE => 'Обновление',
        ];
    }

    /**
     * Тип записи.
     * @return string
     */
    public function getCurrentTagText() {
        $tags = $this->geTags();
        if (array_key_exists($this->tag, $tags)) {
            return $tags[$this->tag];
        }
        return '';
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

}