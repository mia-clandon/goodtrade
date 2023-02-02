<?php

namespace backend\components;

use voku\helper\HtmlMin;
use yii\helpers\ArrayHelper;

use common\libs\cache\TagsCache;
use common\models\Category;

/**
 * Второй вариант рендеринга каталога сразу. Не (ajax) вариант.
 * Class CatalogProcessor
 * @package backend\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class CatalogProcessor {

    /** ключи для кеширования. */
    const TAG_RENDER_KEY = 'tag_render_key';
    const CACHE_RENDER_KEY = 'cache_render_parent_id_';

    /** @var boolean */
    private $enable_cache = false;
    /** @var Category|null */
    private $category_model;
    /** @var null|int */
    private $parent_id;

    /**
     * Рендерит каталог.
     * @return string
     */
    private function process() {
        if ($this->enable_cache) {
            $cached_data = TagsCache::get($this->getCacheKey());
            if (false !== $cached_data) {
                return $cached_data;
            }
        }
        $child_list = $this->getCategoryModel()->getChildCategoryList(
            $this->getParentId(),
            'title ASC'
        );
        $child_list = ArrayHelper::getValue($child_list, Category::CHILD_KEY_PROPERTY, []);
        $rendered_template = $this->renderTemplate($child_list);
        if ($this->enable_cache) {
            $rendered_template = $this->minifyCatalogRenderedHtml($rendered_template);
            TagsCache::set(self::TAG_RENDER_KEY, $this->getCacheKey(), $rendered_template);
        }
        return $rendered_template;
    }

    /**
     * Минификация HTML для хранения в кеше (минимальный размер).
     * @param string $catalog_html
     * @return string
     */
    private function minifyCatalogRenderedHtml($catalog_html) {
        $html_minify = new HtmlMin();
        $html_minify->doRemoveSpacesBetweenTags();
        $html_minify->doRemoveComments();
        $catalog_html = $html_minify->minify($catalog_html);
        return $catalog_html;
    }

    /**
     * @param array $category_list
     * @return string
     */
    private function renderTemplate(array $category_list) {
        return \Yii::$app->getView()->renderFile(\Yii::getAlias('@backend/views/category/parts/categories.php'), [
            'category_list' => $category_list,
        ]);
    }

    /**
     * @return Category
     */
    private function getCategoryModel() {
        if (null === $this->category_model) {
            $this->category_model = new Category();
        }
        return $this->category_model;
    }

    /**
     * @return string
     */
    public function render() {
        return $this->process();
    }

    /**
     * Включить кеширование рендеринга каталога ?.
     * @param bool $flag
     * @return $this
     */
    public function enableCache($flag = true) {
        $this->enable_cache = (boolean)$flag;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheKey() {
        return self::CACHE_RENDER_KEY. (string)$this->getParentId();
    }

    /**
     * @return int
     */
    public function getParentId() {
        return (int)$this->parent_id;
    }

    /**
     * @param int $parent_id
     * @return $this
     */
    public function setParentId($parent_id) {
        $this->parent_id = (int)$parent_id;
        return $this;
    }

    /**
     * Очистка кеша отрендеренного каталога категорий.
     */
    public static function clearCache() {
        TagsCache::clearCacheByTag(self::TAG_RENDER_KEY);
    }
}