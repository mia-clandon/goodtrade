<?php

namespace common\libs\i18n\helper;

use common\libs\i18n\models\Message;
use common\libs\i18n\models\Hint;
use common\libs\traits\Singleton;
use yii\base\Exception;

/**
 * Class SystemLanguages
 * Хелпер для работы с мультиязычностью приложения.
 * @package common\libs\i18n\helper
 * @author Артём Широких kowapssupport@gmail.com
 */
class SystemLanguages {
    use Singleton;

    public const CACHE_KEY_HINT_TRANSLATE = 'hint_translate_key_';

    /** Языковые константы.  ISO-639 - ISO-3166
     * http://www.loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt
     */
    public const LANGUAGE_RUS = 'ru-RU';
    public const LANGUAGE_ENG = 'en-EN';
    public const LANGUAGE_KAZ = 'kk-KZ';

    /** Категории переводов приложения. */
    public const CATEGORY_APP = 'app';

    /** @var array */
    private $errors = [];

    /**
     * Возвращает возможные языки приложения.
     * @return array
     */
    public function getPossibleLanguageCodes(): array {
        return [
            self::LANGUAGE_ENG,
            self::LANGUAGE_RUS,
            self::LANGUAGE_KAZ,
        ];
    }

    /**
     * Возвращает возможные языки приложения без языка источника.
     * @return array
     */
    public function getPossibleLanguageWithoutSource(): array {
        return [
            self::LANGUAGE_KAZ,
            self::LANGUAGE_ENG,
        ];
    }

    /**
     * Возвращает список названий языков.
     * @return array
     */
    public function getLanguageNames(): array {
        return [
            self::LANGUAGE_ENG => 'Английский',
            self::LANGUAGE_KAZ => 'Казахский',
            self::LANGUAGE_RUS => 'Русский',
        ];
    }

    /**
     * Возвращает название языка по коду.
     * @see \common\libs\i18n\helper\SystemLanguages::LANGUAGE_*
     * @param string $language_code
     * @return string
     */
    public function getLanguageName(string $language_code): string {
        return $this->getLanguageNames()[$language_code] ?? '';
    }

    /**
     * Возвращает возвожные категории переводов.
     * @return array
     */
    public function getPossibleCategoryCodes(): array {
        return [
            self::CATEGORY_APP,
        ];
    }

    /**
     * Возвращает список названий категорий.
     * @return array
     */
    public function getCategoryNames(): array {
        return [
            self::CATEGORY_APP => 'Общие переводы приложения',
        ];
    }

    /**
     * Возвращает название категории по коду.
     * @param string $category_code
     * @return string
     */
    public function getCategoryName(string $category_code): string {
        return $this->getCategoryNames()[$category_code] ?? '';
    }

    /**
     * @return string
     */
    public function getAppLanguage(): string {
        // todo: возвращает текущий язык приложения.
    }

    /**
     * @param string $language_code
     * @return bool
     */
    public function switchAppLanguage(string $language_code): bool {
        // todo: смена языка приложения.
    }

    /**
     * @return bool
     */
    public function export(): bool {
        // todo: выгрузка переводов в файлы для хранения в репозитории и возможности импорта на разных окружениях.
    }

    /**
     * @param string $error_message
     * @return $this
     */
    private function addError(string $error_message): self {
        $this->errors[] = $error_message;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearErrors(): self {
        $this->errors = [];
        return $this;
    }

    /**
     * Возвращает перевод хинта на нужном языке.
     * @param int $hint_id
     * @param string $language_code
     * @return string
     */
    public function getTranslate(int $hint_id, string $language_code): string {
        $hint_model = Hint::findOne($hint_id);
        if (null === $hint_model) {
            $this->addError('Хинт не найден.');
            return '';
        }
        if (!\in_array($language_code, $this->getPossibleLanguageCodes(), true)) {
            $this->addError('Код языка не верный.');
            return '';
        }
        if (!$translation = \Yii::$app->cache->get($this->getTranslateCacheKey($hint_id, $language_code))) {
            /** @var Message|null $message */
            $message = Message::find()->where(['id' => $hint_id, 'language' => $language_code])
                ->one();
            if (null === $message) {
                return '';
            }
            $translation = $message->translation;
            \Yii::$app->cache->set($this->getTranslateCacheKey($hint_id, $language_code), $translation);
        }
        return $translation;
    }

    /**
     * @param int $hint_id
     * @param string $language_code
     * @return string
     */
    public function getTranslateCacheKey(int $hint_id, string $language_code): string {
        return self::CACHE_KEY_HINT_TRANSLATE. $hint_id. '_'. $language_code;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * Метод добавляет перевод $source_message на $language_code, текст перевода $translation.
     *
     * @param int $hint_id - Id хинта.
     * @param string $translation - Перевод.
     * @param string $language_code - Код языка.
     *
     * @param string $category_code - Код категории.
     *
     * @return bool
     */
    public function addTranslate(int $hint_id, string $translation, string $language_code, string $category_code = self::CATEGORY_APP): bool {
        if (!\in_array($category_code, $this->getPossibleCategoryCodes(), true)) {
            $this->addError('Код категории не верный.');
            return false;
        }
        if (!\in_array($language_code, $this->getPossibleLanguageCodes(), true)) {
            $this->addError('Код языка не верный.');
            return false;
        }
        if (\Yii::$app->getUser()->getIsGuest()) {
            $this->addError('Гостевой пользователь не может добавлять переводы.');
            return false;
        }
        $hint_model = Hint::findOne($hint_id);
        if (null === $hint_model) {
            $this->addError('Хинт не найден.');
            return false;
        }
        /** @var Message $message_model */
        $message_model = Message::find()->where([
            'id' => $hint_id,
            'language' => $language_code,
        ])->one();
        if (null === $message_model) {
            $message_model = new Message();
            $message_model->id = $hint_id;
        }
        // add translate message.
        $message_model->language = $language_code;
        $message_model->translation = $translation;
        $message_model->user_id = \Yii::$app->getUser()->id;
        $result = $message_model->save();
        if (!$result) {
            foreach ($message_model->getErrors() as $error) {
                $this->addError($error);
            }
        }
        if ($result) {
            \Yii::$app->cache->set($this->getTranslateCacheKey($hint_id, $language_code), null);
        }
        return $result;
    }
}