<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;

use common\libs\Logger;
use common\models\firms\Profile;
use common\models\Location;

/**
 * Class FirmProfileParserController
 * php yii firm-profile-parser/location
 * @package console\controllers
 */
class FirmProfileParserController extends Controller {

    /**
     * Запуск парсера регионов.
     */
    public function actionLocation() {
        $this->stdout("Начало парсинга городов FirmProfiles ...". "\n", Console::BOLD);

        $cities = (new Location())->getCitiesArray();
        /** @var Profile[] $profiles */
        $profiles = Profile::find()->all();

        $not_found_localities = [];
        foreach ($profiles as $profile) {
            $city_id = $this->strPosArray($profile->locality, $cities);
            // такого города не нашлось в нашей базе с городами.
            if (is_null($city_id)) {
                $not_found_localities[] = $profile->locality;
                continue;
            }
            $profile->city_id = (int)$city_id;
            $profile->save(true, ['city_id']);
        }
        Logger::get()->info(implode(PHP_EOL, $not_found_localities));

        $this->stdout("Города которые не были найдены в базе записаны в лог...". "\n", Console::BOLD);
        $this->stdout("End...". "\n", Console::BOLD);
    }

    /**
     * Ищет в массиве вхождение $needle и возвращает ключ найденного элемента либо null.
     * @param string $haystack
     * @param array $needles
     * @param int $offset
     * @return int|null|string
     */
    private function strPosArray($haystack, array $needles, $offset = 0) {
        foreach ($needles as $key => $needle) {
            if (strpos(mb_strtolower($haystack), mb_strtolower($needle), $offset)) {
                return $key;
            }
        }
        return null;
    }
}