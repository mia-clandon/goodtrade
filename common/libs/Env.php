<?php

namespace common\libs;

use common\libs\traits\Singleton;
use Yii;

/**
 * Class Env
 * @package common\libs
 */
class Env
{
    use Singleton;

    private array|null $env = null;

    /**
     * @param string $param_name
     * @param null $default_value
     * @return string
     */
    public function get(string $param_name, $default_value = null): string
    {
        return env($param_name, $default_value);
    }

    /**
     * Метод проверяет, на продакшене ли выполняется приложение.
     * @return bool
     */
    public function isProd(): bool
    {
        return $this->get('YII_ENV') === 'prod';
    }

    public function getFrontendUrl(): string
    {
        return $this->get('BASE_DOMAIN');
    }

    public function getBackendUrl(): string
    {
        return $this->get('BASE_ADMIN_DOMAIN');
    }

    /**
     * Метод проверяет, текущее окружение backend ?
     * @return bool
     */
    public function isBackendApp(): bool
    {
        return strpos(Yii::getAlias('@app'), 'backend');
    }

    /**
     * Метод проверяет, текущее окружение frontend ?
     * @return bool
     */
    public function isFrontendApp(): bool
    {
        return strpos(Yii::getAlias('@app'), 'frontend');
    }
}