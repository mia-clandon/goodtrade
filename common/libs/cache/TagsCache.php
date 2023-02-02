<?php

namespace common\libs\cache;

use Yii;

/**
 * Class TagsCache
 * @package common\libs\cache
 * @author yerganat
 */
class TagsCache {

    /** Префикс для кеширования тегов. */
    const TAG_PREFIX = 'tag_prefix';

    /**
     * Возвращает ключи кеширования по названию тега.
     * @param string $tag_name
     * @return array
     */
    public static function getKeysByTag($tag_name) {
        $keys = Yii::$app->cache->get(self::TAG_PREFIX. $tag_name);
        if (!$keys) {
            return [];
        }
        return $keys;
    }

    /**
     * @param string $tag_name
     * @return bool
     */
    public static function removeKeysByTag($tag_name) {
        return Yii::$app->cache->set(self::TAG_PREFIX. $tag_name, null);
    }

    /**
     * Метод работает идентично Yii::$app->cache->set
     * за исключением того что привязывает ключ к тегу.
     * @param string $tag
     * @param string $key
     * @param mixed $value
     * @param int $duration
     * @return bool
     */
    public static function set($tag, $key, $value, $duration = null) {
        // сохранение ключей в тег.
        $keys_by_tag = static::getKeysByTag($tag);
        if (!in_array($key, $keys_by_tag)) {
            $keys_by_tag[] = $key;
            Yii::$app->cache->set(self::TAG_PREFIX. $tag, $keys_by_tag);
        }
        return Yii::$app->cache->set($key, $value, $duration);
    }

    /**
     * Очистка кеша по тегу.
     * @param string $tag
     * @return bool
     */
    public static function clearCacheByTag($tag) {
        $keys = static::getKeysByTag($tag);
        foreach ($keys as $key) {
            Yii::$app->cache->delete($key);
        }
        static::removeKeysByTag($tag);
        return true;
    }

    /**
     * @see \yii\caching\Cache::get
     * @param string $key
     * @return mixed
     */
    public static function get($key) {
        return Yii::$app->cache->get($key);
    }
}