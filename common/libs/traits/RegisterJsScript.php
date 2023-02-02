<?php

namespace common\libs\traits;

use common\libs\Env;
use common\libs\StringBuilder;

use yii\db\Exception;
use yii\log\Logger;

/**
 * Регистрация скриптов.
 * Class RegisterJsScript
 * @package common\libs\traits
 * @author Артём Широких kowapssupport@gmail.com
 */
trait RegisterJsScript
{

    /**
     * Переопределяется в используемых трейт - классах.
     */
    protected function registerFormAssets()
    {
    }

    /**
     * Регистрирует скрипты того класса - который вызвал метод.
     * @param array $options
     */
    protected function registerOnlyThisClassScript(array $options = [])
    {
        $this->register(__CLASS__, $options);
    }

    /**
     * Регистрирует скрипты последнего наследника.
     * @param array $options
     */
    protected function registerJsScript(array $options = [])
    {
        $this->register(get_class($this), $options);
    }

    /**
     * Регистрация скриптов.
     * @param string $class_name
     * @param array $options
     */
    private function register(string $class_name, array $options = []): void
    {
        $this->registerFormAssets();

        $reflector = new \ReflectionClass($class_name);
        $file_name = str_replace(\Yii::getAlias('@app'), '/js', $reflector->getFileName());
        $file_name = str_replace('php', 'js', $file_name);
        $file_name = strtolower($file_name);

        // возможно в текущей директории есть папка dist - в которой лежат минифицированные версии скрипта.
        try {
            $path_info = pathinfo($file_name);
            $minified_file_name = (new StringBuilder())
                ->setComma(DIRECTORY_SEPARATOR)
                ->add(\Yii::getAlias('@app/web'), false)
                ->add($path_info['dirname'])
                ->add('dist')
                ->add($path_info['basename'], false);
            $minified_file_name = $minified_file_name->get();
            $has_min_file = (bool)file_exists($minified_file_name);

            if ($has_min_file) {
                $this->registerScript(str_replace(\Yii::getAlias('@app/web'), '', $minified_file_name), $options);
                return;
            }
        } catch (Exception $exception) {
            \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
        }
        if (file_exists(\Yii::getAlias('@app/web') . $file_name)) {
            $this->registerScript($file_name, $options);
        }
    }

    /**
     * @param string $url
     * @param array $options
     */
    private function registerScript($url, array $options = [])
    {
        if (!$options && Env::i()->isBackendApp()) {
            $options = [
                'depends' => 'backend\assets\AppAsset',
            ];
        }
        \Yii::$app->getView()->registerJsFile($url, $options);
    }
}