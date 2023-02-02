<?

namespace frontend\components\lib\notification\traits;

use yii\helpers\ArrayHelper;

/**
 * trait Placeholders
 * Трейт работает с плейсхолдерами в тексте.
 * @package frontend\components\lib\notification\traits
 * @author Артём Широких kowapssupport@gmail.com
 */
trait Placeholders {

    protected function getPlaceholders() {
        return [];
    }

    /**
     * Метод производит замену плейсхолдеров на необходимые данные.
     * @param string $text_placeholder
     * @param string $content
     * @return string
     */
    protected function replacePlaceholdersData($text_placeholder, $content) {
        return $content;
    }

    /**
     * Замена плейсхолдеров в тексте уведомления.
     * @param string $content - текст с плейсхолдерами.
     * @return string
     */
    private function replacePlaceholders($content) {

        if (!$this->textHasPlaceholders($content)) {
            return $content;
        }

        $placeholders = $this->getPlaceholders();
        $placeholders_from_text = $this->getPlaceholdersFromText($content);

        foreach ($placeholders_from_text as $text_placeholder) {
            if (in_array($text_placeholder, $placeholders)) {
                $content = $this->replacePlaceholdersData($text_placeholder, $content);
            }
        }

        return $content;
    }

    /**
     * Заменяет плейсхолдер на значение в тексте.
     * @param string $placeholder
     * @param string $content
     * @param string $text
     * @return string
     */
    private function replacePlaceholder($placeholder, $content, $text) {
        return str_replace($placeholder, $content, $text);
    }

    /**
     * Метод проверяет, есть ли в тексте плейсхолдеры.
     * @param string $text
     * @return boolean
     */
    private function textHasPlaceholders($text) {
        return count($this->getPlaceholdersFromText($text)) > 0;
    }

    /**
     * Получение плейсхолдеров с текста.
     * @param string $text
     * @param bool $with_wrap
     * @return array
     */
    private function getPlaceholdersFromText($text, $with_wrap = true) {

        $regular_expression = '/[{a-z_\-}]+/i';
        preg_match_all($regular_expression, $text, $matches);
        $matches = ArrayHelper::getValue($matches, '0', []);
        $matches = array_unique($matches);

        // если обёртка не нужна.
        if (!$with_wrap) {
            $matches = array_map(function ($match) {
                return str_replace('{', '', str_replace('}', '', $match));
            }, $matches);
        }

        return $matches;
    }
}