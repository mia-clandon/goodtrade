<?php

$env_config = null;

/**
 * Получение значения из .env
 * @param string $param_name
 * @param mixed $default_value
 * @return string
 */
function env(string $param_name, mixed $default_value = ''): string
{
    global $env_config;
    if ($env_config === null) {
        $env_config = \Dotenv\Dotenv::createArrayBacked(__DIR__ . '/config/')->load();
    }
    return $env_config[$param_name] ?? '';
}

function dd($var)
{
    \common\libs\VarDumper::dump($var);
}

function arr_get_val(array $array, string|int $key, mixed $default_value = ''): mixed
{
    try {
        return \yii\helpers\ArrayHelper::getValue($array, $key, $default_value);
    } catch (Exception $e) {
        return $default_value;
    }
}