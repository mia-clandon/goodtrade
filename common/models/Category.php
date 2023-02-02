<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use common\libs\Storage;
use common\libs\cache\TagsCache;
use common\models\base\Category as BaseCategory;
use common\models\firms\Categories as FirmsCategories;
use common\models\goods\Categories as GoodsCategories;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $icon_class
 * @property string $small_text
 * @property string $text
 * @property integer $parent
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class Category extends BaseCategory {

    const TABLE_NAME = 'category';

    /** @see: \common\models\Category::getChildListByParent */
    const TAG_CHILD_BY_PARENT = 'tag_child_by_parent';
    const CACHE_CHILD_BY_PARENT = 'cache_child_by_parent_';

    /** @var array */
    public $images = [];

    public static function tableName() {
        return self::TABLE_NAME;
    }

    /**
     * Возможные иконки сфер деятельностей.
     * @return array
     */
    public function getPossibleIconClasses() {
        return [
            'agriculture' => 'Сельское хозяйство',
            'forest' => 'Лес',
            'textile_and_clothing' => 'Текстиль, Одежда',
            'minerals_and_chemicals_metallurgy' => 'Добыча и Химия, Металлургия',
            'engineering' => 'Машиностроение',
            'instrumentation' => 'Приборостроение',
            'construction' => 'Стройка',
            'containers_and_packaging' => 'Тара и Упаковка',
            'medicine' => 'Медицина',
            'energetics' => 'Энергетика',
            'food' => 'Пищевая промышленность',
        ];
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['parent'], 'integer'],
            [['title', 'meta_keywords'], 'string', 'max' => 255],
            [['icon_class'], 'string', 'max' => 50],
            [['small_text', 'text', 'meta_description'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages() {
        return $this->hasMany(CategoryImage::class, ['category_id' => 'id']);
    }

    /**
     * Возвращает главную фотографию категории.
     * @return null|string
     */
    public function getMainImage() {
        /** @var CategoryImage|null $image */
        $image = $this->getImages()->orderBy('position')->one();
        return null === $image ? null : $image->image;
    }

    /**
     * @return ActiveQuery
     */
    public function getOked() {
        return $this->hasMany(Oked::class, ['key' => 'oked'])
            ->viaTable(CategoryOked::tableName(), ['category_id' => 'id']);
    }

    /**
     * Возвращает ActiveQuery связанных категорий.
     * @param int $type
     * @return ActiveQuery
     */
    public function getCategoryRelatedQuery($type = CategoryRelation::TYPE_RELATION_DUPLICATE) {
        return $this->hasMany(Category::class, ['id' => 'related_category_id'])
            ->viaTable(CategoryRelation::tableName(), ['category_id' => 'id'], function ($query) use($type) {
            /* @var $query \yii\db\ActiveQuery */
            $query->andWhere(['type' => $type]);
        });
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->updateImages();
    }

    /**
     * Устанавливает фото категорий в модель.
     * @param array $images
     * @return $this
     */
    public function setImages(array $images) {
        $images = array_filter($images, function($image) {
            return trim($image) !== '';
        });
        $this->images = $images;
        return $this;
    }

    /**
     * Метод возвращает список категорий по родительской.
     * @param int $parent_id
     * @return array
     */
    public function getChildListByParent($parent_id) {
        $parent_id = (int)$parent_id;
        $cache_key = $this->getChildByParentCacheKey($parent_id);
        if (!$category_list = TagsCache::get($cache_key)) {
            $sub_query = (new Query())
                ->select((new Expression('COUNT(*)')))
                ->from(Category::tableName())
                ->where(['parent' => new Expression('t.id')])
            ;
            $category_list = Category::find()
                ->select(['id', 'title', 'parent', 'icon_class',
                    new Expression('IF (('.$sub_query->createCommand()->rawSql.'), true, false) as `has_child`')
                ])
                ->alias('t')
                ->where(['parent' => $parent_id])
                ->orderBy('created_at ASC')
                ->asArray()
                ->all();
            $category_list = array_map(function ($category) {
                $category['id'] = (int)$category['id'];
                $category['parent'] = (int)$category['parent'];
                $category['has_child'] = (int)$category['has_child'] > 0;
                return $category;
            }, $category_list);
            TagsCache::set(self::TAG_CHILD_BY_PARENT, $cache_key, $category_list);
        }
        return $category_list;
    }

    /**
     * Возвращает самую корневую категорию/Сферу деятельности.
     * @return Category
     */
    public function getActivity() {
        //todo; переделать в пользу 1го запроса.;
        $root = $this;
        /** @var Category $parent */
        do {
            $parent = $root->getParent()->one();
            if ($parent == null) {
                return $root;
            }
            $root = $parent;
        }
        while(true);
        return $root;
    }

    /**
     * Возвращает ключ для кеша.
     * @see \common\models\Category::getChildListByParent
     * @param int $parent_id
     * @return string
     */
    private function getChildByParentCacheKey($parent_id) {
        return self::CACHE_CHILD_BY_PARENT. $parent_id;
    }

    /**
     * Устанавливает порядок фото категории.
     * @param array $images_order
     * @return $this
     */
    public function setImagesOrder(array $images_order) {
        /** @var CategoryImage[] $images */
        $images = $this->getImages()->all();
        $images_order = array_flip($images_order);

        foreach ($images as $image) {
            $image->position = $images_order[$image->id] + 1;
            $image->save();
        }
        return $this;
    }

    /**
     * Сохраняет связку категории к окед'у.
     * @param array $items
     * @param boolean $remove_not_exist -
     */
    public function setCategoryOked(array $items, $remove_not_exist = true) {
        if ($remove_not_exist) {
            // удаляю связи которые есть в базе но нет в $items;
            $current_category_oked = CategoryOked::find()->where(['category_id' => $this->id])
                ->select(['oked'])
                ->asArray()
                ->all();
            $current_category_oked = ArrayHelper::getColumn($current_category_oked, 'oked');
            $for_remove_relations = array_diff($current_category_oked, $items);
            CategoryOked::deleteAll(['category_id' => $this->id, 'oked' => $for_remove_relations]);
        }
        // добавляю новые связи.
        foreach ($items as $item) {
            $oked = intval($item);
            $relation = new CategoryOked();
            $relation->setAttributes([
                'category_id' => $this->id,
                'oked' => $oked,
            ]);
            $relation->save();
        }
    }

    /**
     * Метод проверяет, используется ли категория в организациях.
     * @return bool
     */
    public function isUseInFirm() {
        return (bool)FirmsCategories::find()->where(['activity_id' => $this->id])->count() > 0;
    }

    /**
     * @return bool
     */
    public function isUseInProduct() {
        return (bool)GoodsCategories::find()->where(['category_id' => $this->id])->count() > 0;
    }

    /**
     * Добавляет фотографии категории.
     * @return bool
     */
    public function updateImages() {
        $images = (array)$this->images;
        if (empty($images)) {
            return true;
        }

        $max_position = $this->getImages()->max('position');
        $uploaded_files = Storage::i()->moveFilesToStorage($images, self::TABLE_NAME);
        foreach ($uploaded_files as $file) {
            $image = new CategoryImage();
            $image->category_id = (int)$this->id;
            $image->image = (string)$file;
            $image->position = ++$max_position;
            $image->save();
        }
        return true;
    }

    public function clearCache() {
        TagsCache::clearCacheByTag(self::TAG_CHILD_BY_PARENT);
        return parent::clearCache();
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'title' => 'Название категории',
            'meta_keywords' => 'Ключевые слова',
            'meta_description' => 'Ключевое описание',
            'parent' => 'Родительская категория',
            'small_text' => 'Краткое описание',
            'text' => 'Полное описание',
            'icon_class' => 'Класс иконки',
            'created_at' => 'Дата создания',
        ];
    }
}