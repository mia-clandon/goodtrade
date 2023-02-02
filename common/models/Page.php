<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "pages".
 *
 * @property integer $id
 * @property string $title
 * @property string $alias
 * @property string $text
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class Page extends Base {

    const TABLE_NAME = 'pages';

    #region - свойства модели
    const PROPERTY_TITLE        = 'title';
    const PROPERTY_ALIAS        = 'alias';
    const PROPERTY_TEXT         = 'text';
    const PROPERTY_CREATED_AT   = 'created_at';
    const PROPERTY_UPDATED_AT   = 'updated_at';
    #endregion;

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['title', 'alias', 'text'], 'required'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    /**
     * Экспорт данных в JSON.
     * @return string
     */
    public function export() {
        $pages = self::find()->all();
        $for_export = [];
        /** @var Page[] $pages */
        foreach ($pages as $page) {
            $for_export[] = [
                self::PROPERTY_TITLE      => $page->title,
                self::PROPERTY_ALIAS      => $page->alias,
                self::PROPERTY_TEXT       => $page->text,
                self::PROPERTY_CREATED_AT => $page->created_at,
                self::PROPERTY_UPDATED_AT => $page->updated_at,
            ];
        }
        return Json::encode($for_export);
    }

    /**
     * Импортирует страницы сайта.
     * @param array $rows
     * @return bool|array
     */
    public function import(array $rows) {
        $errors = [];
        // останавливаю импорт если нет строк.
        if (!count($rows)) {
            return true;
        }
        // удаление старных страниц.
        Page::deleteAll();
        // загрузка новых.
        foreach ($rows as $row) {
            $page = new Page();
            $page->setAttributes([
                    self::PROPERTY_TITLE        => ArrayHelper::getValue($row, self::PROPERTY_TITLE),
                    self::PROPERTY_ALIAS        => ArrayHelper::getValue($row, self::PROPERTY_ALIAS),
                    self::PROPERTY_TEXT         => ArrayHelper::getValue($row, self::PROPERTY_TEXT),
                    self::PROPERTY_CREATED_AT   => ArrayHelper::getValue($row, self::PROPERTY_CREATED_AT),
                    self::PROPERTY_UPDATED_AT   => ArrayHelper::getValue($row, self::PROPERTY_UPDATED_AT),
                ]);
            $result = $page->save();
            if (!$result) {
                $errors[$page->title] = $page->getErrors();
            }
        }
        return (empty($errors)) ? true : $errors;
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Название страницы',
            'alias' => 'ЧПУ алиас',
            'text' => 'Содержимое страницы',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
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
}