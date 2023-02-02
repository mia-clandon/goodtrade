<?php

namespace common\libs\parser;

use common\libs\StringBuilder;
use common\libs\traits\Singleton;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\models\Speller as Model;

/**
 * Class Speller
 * @package common\libs\parser
 * @author Артём Широких kowapssupport@gmail.com
 */
class Speller extends Parser {

    use Singleton;

    /** @var string  */
    protected $url = 'http://speller.yandex.net/services/spellservice.json/checkText';

    /**
     * @param $text
     * @return string
     */
    public function get($text) {

        $words = $this->prepareText($text);

        $cached_result = [];

        /** @var Model[] $cached_words */
        $cached_words = Model::find()
            ->select(['word', 'corrected'])
            ->where(['word' => $words])
            ->all();

        foreach ($cached_words as $cached_word) {

            $cached_result[$cached_word->word] = $cached_word->corrected;
        }

        // слова которые отсутсвуют в базе.
        $exist_cache = array_diff($words, array_values($cached_result));

        // может уже есть в базе как правильные ?
        $cached_words = Model::find()
            ->select(['word', 'corrected'])
            ->where(['corrected' => $exist_cache])
            ->all();

        foreach ($cached_words as $cached_word) {

            $cached_result[$cached_word->corrected] = $cached_word->corrected;
        }

        // слова которые отсутсвуют в базе.
        $exist_cache = array_diff($words, array_keys($cached_result));

        // отсутствующие слова отдаю яндексу на проверку.
        $speller_result = $this->checkText(implode(' ', $exist_cache));

        foreach ($speller_result as $speller) {

            $word = ArrayHelper::getValue($speller, 'word', null);
            $corrected = ArrayHelper::getValue($speller, 's.0', null);

            if (!is_null($word) && !is_null($corrected)) {

                $cached_result[$word] = $corrected;
                if (!is_numeric($word)) {
                    $this->saveInCache($word, $corrected);
                }
            }
        }

        // слова которые отсутсвутют в базе и о которых не знает яндекс.
        $exist_cache_speller = array_diff($words, array_keys($cached_result));

        // сохраняю их в базе.
        foreach ($exist_cache_speller as $word) {
            $cached_result[$word] = $word;
            if (!is_numeric($word)) {
                $this->saveInCache($word, $word);
            }
        }

        // собираю результат обратно
        $result = new StringBuilder();

        foreach ($words as $word) {

            if (array_key_exists($word, $cached_result)) {
                $result->setComma(' ');
                $result->add($cached_result[$word]);
            }
        }
        return rtrim($result->get(), ' ');
    }

    /**
     * @param string $word
     * @param string $corrected
     */
    private function saveInCache($word, $corrected) {
        $speller_entity = new Model();
        $speller_entity->word = $word;
        $speller_entity->corrected = $corrected;
        $speller_entity->save();
    }

    /**
     * @param string $text
     * @return array
     */
    private function prepareText($text) {
        $text = preg_replace('/\s+/', ' ', trim($text));
        $words = explode(' ', $text);
        $words = array_map('mb_strtolower', $words);
        return $words;
    }

    /**
     * @param string $text
     * @return array
     */
    public function checkText($text) {

        $text = (string)$text;

        if (empty($text)) {
            return [];
        }

        $this->setPostData(['text' => $text]);

        $content = $this->getContent();
        $content = Json::decode($content);

        return $content;
    }
}