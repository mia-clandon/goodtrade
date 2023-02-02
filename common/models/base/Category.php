<?php

namespace common\models\base;

use yii\db\Expression;
use yii\db\Query;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

use common\libs\cache\TagsCache;
use common\models\Base;

use backend\components\CatalogProcessor;

/**
 * @property integer $id
 * @property string $title
 * @property integer $parent
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class Category extends Base {

    const CHILD_KEY_PROPERTY = 'child';

    const PROPERTY_KEY_ID = 'id';
    const PROPERTY_KEY_TITLE = 'title';
    const PROPERTY_KEY_PARENT = 'parent';
    const PROPERTY_KEY_ICON_CLASS = 'icon_class';
    const PROPERTY_KEY_CREATED_AT = 'created_at';
    const PROPERTY_KEY_UPDATED_AT = 'updated_at';
    const PROPERTY_KEY_HAS_CHILD = 'has_child';

    #region Теги кеширования.
    /** дочерние категории. @see: \common\models\base\Category::getChildCategoryList */
    const TAG_CHILD_CATEGORY_LIST = 'child_category_list';

    /** опции для select'a. @see: \common\models\base\Category::getNestedOptions */
    const TAG_NESTED_OPTIONS = 'tag_nested_options';
    #endregion

    /** @var string Ключ кеша родительских категорий. @see \common\models\base\Category::getAllParentIds */
    protected $key_cache_parent_ids = 'cache_category_parent_ids_';

    /** @var string Ключ кеша дочерних категорий. @see \common\models\base\Category::getAllChildIds */
    protected $key_cache_child_ids = 'cache_category_child_ids_';

    /** @var string Ключ получения опций для Select'a. @see \common\models\base\Category::getNestedOptions */
    protected $key_cache_nested_options = 'cache_nested_options_';

    /** @var string - дочерние категории. @see \common\models\base\Category::getChildCategoryList */
    protected $key_child_category_list = 'cache_child_category_list_';

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Заполняет массив out категориями с дочерними категориями.
     * @param int $parent
     * @param array $out
     */
    protected function fillCategoryList(int $parent, array &$out): void {
        /** @var static[] $categories */
        $categories = self::find()->where(['parent' => $parent])->all();

        foreach ($categories as $category) {
            $out[$category->id] = $this->getCategoryDataRow($category);
            $this->fillCategoryList($category->id, $out[$category->id]['nodes']);
        }
    }

    /**
     * @param Category $category
     * @return array
     */
    protected function getCategoryDataRow(Category $category) {
        return [
            'id'            => $category->id,
            'text'          => $category->title,
            'parent'        => $category->parent,
        ];
    }

    /**
     * Получение списка дочерних категорий.
     * @param integer|null $category_id
     * @param string|null $order_by
     * @return array
     */
    public function getChildCategoryList($category_id = null, $order_by = null) {
        if (is_null($category_id) && $this->isNewRecord) {
            return [];
        }
        $key = $this->key_child_category_list.'_'.$category_id;
        if (!$out = TagsCache::get($key)) {
            $category_id = (is_null($category_id)) ? $this->id : $category_id;
            $out = Category::find()
                ->where(['id' => (int)$category_id]);
            if (null !== $order_by) {
                $out->orderBy($order_by);
            }
            $out = $out->asArray()->one();
            if(null === $out) {
                $out = [];
            }
            $this->fillChildCategoryList($category_id, $out, $order_by);
            TagsCache::set(self::TAG_CHILD_CATEGORY_LIST, $key, $out);
        }
        return $out;
    }


    /**
     * Метод отдаёт массив с прямыми дочерними категориями.
     * todo: сделать кеширование.
     * @param int $parent_id
     * @return array
     */
    public function getFirstChildCategoryList($parent_id) {
        $parent_id = (int)$parent_id;
        $sub_query = (new Query())
            ->select((new Expression('COUNT(*)')))
            ->from(Category::tableName())
            ->where(['parent' => new Expression('t.id')])
        ;
        /** @var Category[] $category_list */
        $category_list = Category::find()
            ->select(['id', 'title', 'parent', 'icon_class',
                new Expression('IF (('.$sub_query->createCommand()->rawSql.'), true, false) as `has_child`')
            ])
            ->where(['parent' => (int)$parent_id])
            ->orderBy('id DESC')
            ->alias('t')
            ->asArray()
            ->all();
        return $category_list;
    }

    /**
     * @param int $parent_id
     * @param array $out
     * @param string|null $order_by
     */
    private function fillChildCategoryList($parent_id, array &$out, $order_by = 'created_at ASC') {

        $parent_id = (int)$parent_id;

        $sub_query = (new Query())
            ->select((new Expression('COUNT(*)')))
            ->from(Category::tableName())
            ->where(['parent' => new Expression('t.id')])
        ;
        /** @var array $child_category_list */
        $child_category_list = Category::find()
            ->select(['id', 'title', 'parent', 'icon_class',
                  new Expression('IF (('.$sub_query->createCommand()->rawSql.'), true, false) as `has_child`')
            ])
            ->alias('t')
            ->where(['parent' => (int)$parent_id])
            ->orderBy($order_by)
            ->asArray()
            ->all()
        ;
        foreach ($child_category_list as $child_category_item) {
            $item_id = (int)$child_category_item['id'];
            $out[self::CHILD_KEY_PROPERTY][$item_id] = $child_category_item;
            $temp_link = &$out[self::CHILD_KEY_PROPERTY][$item_id];
            $this->fillChildCategoryList($item_id, $temp_link);
        }
    }

    /**
     * Получение древа для Select'a.
     * @param int $parent_category_id
     * @return array
     */
    public function getNestedOptions($parent_category_id = 0) {
        $key = $this->key_cache_nested_options. $parent_category_id;
        if (!$out = TagsCache::get($key)) {
            $out = [];
            $this->fillCategoryListOptions($parent_category_id, $out, 0, '--');
            TagsCache::set(self::TAG_NESTED_OPTIONS, $key, $out);
        }
        return $out;
    }

    /**
     * Рекурсивно заполняет массив с категориями (для опций Select).
     * @param int $parent - id родителя
     * @param array $out - заполняемый массив
     * @param int $level - уровень вложенности
     * @param string $delimiter
     */
    private function fillCategoryListOptions($parent, &$out, $level = 0, $delimiter = '------') {
        /** @var static[] $categories */
        $categories = self::find()->where(['parent' => $parent])->all();
        $separator = ($level > 0) ? implode('',array_fill(1, $level, $delimiter)) : '';
        foreach ($categories as $key => $category) {
            $out[$category->id] = trim($separator.' '.$category->title);
            $this->fillCategoryListOptions($category->id, $out, $level + 1);
        }
    }

    /**
     * Дочерние категории.
     * @return ActiveQuery
     */
    public function getChild() {
        return $this->hasMany(self::class, ['parent' => 'id']);
    }

    /**
     * Родительская категория.
     * @return ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(self::class, ['id' => 'parent']);
    }

    /**
     * Возвращает массив с id всех родительских категорий.
     * @param null|int $category_id
     * @return array
     */
    public function getAllParentIds($category_id = null) {
        $category_id = (null === $category_id) ? $this->id : $category_id;
        $key = $this->key_cache_parent_ids. $category_id;
        if (!$id_list = \Yii::$app->cache->get($key)) {
            $parents = []; $id_list = [];
            $this->getAllParents($this, $parents);
            foreach ($parents as $parent) {
                $id_list[] = $parent->id;
            }
            \Yii::$app->cache->set($key, $id_list);
        }
        return $id_list;
    }

    /**
     * Возвращает массив с id всех дочерних категорий.
     * @param null|int $category_id
     * @return array
     */
    public function getAllChildIds($category_id = null) {
        $category_id = (null === $category_id) ? $this->id : $category_id;
        $key = $this->key_cache_child_ids. $category_id;
        if (!$id_list = \Yii::$app->cache->get($key)) {
            $child = []; $id_list = [];
            $this->getAllChildren($this, $child);
            foreach ($child as $category) {
                $id_list[] = $category->id;
            }
            \Yii::$app->cache->set($key, $id_list);
        }
        return $id_list;
    }

    /**
     * Заполняет массив $out родительскими категориями.
     * @param Category $category
     * @param Category[] $out
     */
    public function getAllParents(Category $category, array &$out) {
        if ($category) {
            /** @var Category $parent */
            $parent = $category->getParent()->one();
            if ($parent) {
                $out[] = $parent;
                $this->getAllParents($parent, $out);
            }
        }
    }

    /**
     * Заполняет массив $out дочерними категориями.
     * @param Category $category
     * @param Category[] $out
     */
    private function getAllChildren(Category $category, array &$out) {
        if ($category) {
            /** @var Category[] $child_categories */
            $child_categories = $category->getChild()
                ->select(['id', 'title'])
                ->orderBy('created_at ASC')
                ->all();
            foreach ($child_categories as $child_category) {
                $out[] = $child_category;
                $this->getAllChildren($child_category, $out);
            }
        }
    }

    /**
     * @return bool
     */
    public function hasChild() {
        return (bool)$this->getChild()->count();
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

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->clearCache();
    }

    /**
     * @return bool
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            $this->clearCache();
        }
        return true;
    }

    /**
     * Очистка кеша категорий.
     * @return $this
     */
    public function clearCache() {
        CatalogProcessor::clearCache();
        TagsCache::clearCacheByTag(self::TAG_CHILD_CATEGORY_LIST);
        TagsCache::clearCacheByTag(self::TAG_NESTED_OPTIONS);
        \Yii::$app->cache->delete($this->key_cache_child_ids);
        \Yii::$app->cache->delete($this->key_cache_parent_ids);
        return $this;
    }
}