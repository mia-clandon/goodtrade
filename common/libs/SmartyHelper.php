<?php

namespace common\libs;

use common\libs\traits\Singleton;
use yii\helpers\ArrayHelper;

/**
 * Class SmartyHelper
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class SmartyHelper {

    use Singleton;

    /**
     * @param bool $use_cache
     * @return \Smarty
     */
    public function getSmartyObject($use_cache = true) {
        $smarty = new \Smarty();
        $smarty_config = ArrayHelper::getValue(\Yii::$app->params, 'form.template', false);
        $caching = (bool)$smarty_config ? ArrayHelper::getValue($smarty_config, 'caching', true) : true;
        $smarty->caching = (!$use_cache) ? $use_cache : $caching;
        if ($caching) {
            $cache_lifetime = ArrayHelper::getValue($smarty_config, 'cache_time', false);
            if ($cache_lifetime) {
                $smarty->setCacheLifetime($cache_lifetime);
            }
            $smarty->setCacheDir(\Yii::getAlias('@runtime/forms/cache'));
            $smarty->setCompileDir(\Yii::getAlias('@runtime/forms/compile'));
        }
        $debugging = ArrayHelper::getValue($smarty_config, 'debugging', false);
        if ($debugging) {
            $smarty->debugging = $debugging;
        }
        return $smarty;
    }
}