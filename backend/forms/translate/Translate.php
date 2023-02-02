<?php

namespace backend\forms\translate;

use backend\components\form\controls\Button;
use backend\components\form\controls\Input;
use backend\components\form\controls\TextArea;
use backend\components\form\Form;
use common\libs\i18n\helper\SystemLanguages;
use common\libs\i18n\models\Hint;
use yii\helpers\ArrayHelper;

/**
 * Class Translate
 * @package backend\forms\translate
 * @author Артём Широких kowapssupport@gmail.com
 */
class Translate extends Form {

    /** @var null|int */
    private $hint_id;
    /** @var null|string */
    private $hint_text;

    public function initControls(): void {
        parent::initControls();
        $this->addClass('add-translate-form');

        $textarea_controls = [];
        foreach(SystemLanguages::i()->getPossibleLanguageWithoutSource() as $language_code) {
            $language_name = SystemLanguages::i()->getLanguageName($language_code);
            $control = (new TextArea())
                ->setName($this->getTranslateNameByLanguageCode($language_code))
                ->setTitle('Перевод текста "'.$this->hint_text.'" на "'.$language_name.'".')
                ->setRowsCount(7)
                ->setValue($this->getTranslate($language_code));
            $this->registerControl($control);
            $textarea_controls[$language_code] = $control->render();
        }

        $button_control = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY);
        $this->registerControl($button_control);

        $hidden_control = (new Input())
            ->setName('hint_id')
            ->setType(Input::TYPE_HIDDEN)
            ->setValue($this->hint_id);
        $this->registerControl($hidden_control);

        /** возможные языки для переводов. */
        $this->addTemplateVars([
            'tabs' => $this->getTabItems(),
            'textarea_controls' => $textarea_controls,
        ]);
    }

    /**
     * @param string $language_code
     * @return string
     */
    private function getTranslateNameByLanguageCode(string $language_code): string {
        return 'translate['.$language_code.']';
    }

    /**
     * @param int $hint_id
     * @return $this
     */
    public function setHintId(int $hint_id): self {
        $this->hint_id = $hint_id;
        return $this;
    }

    /**
     * @param string $hint_text
     * @return $this
     */
    public function setHintText(string $hint_text): self {
        $this->hint_text = htmlspecialchars($hint_text);
        return $this;
    }

    /**
     * @return array
     */
    private function getTabItems(): array {
        $result = [];
        foreach (SystemLanguages::i()->getPossibleLanguageWithoutSource() as $language_code) {
            $result[$language_code] = SystemLanguages::i()->getLanguageName($language_code);
        }
        return $result;
    }

    /**
     * @param string $language_code
     * @return string
     */
    public function getTranslate(string $language_code): string {
        if (null !== $this->hint_id) {
            return SystemLanguages::i()->getTranslate($this->hint_id, $language_code);
        }
        return '';
    }

    /**
     * Сохранение переводов.
     * @return bool
     */
    public function save(): bool {
        $data = $this->getControlsData();
        $hint_id = (int)ArrayHelper::getValue($data, 'hint_id');
        $hint = Hint::getHintById($hint_id);
        if (null === $hint) {
            $this->addError('submit', 'Хинт не найден.');
            return false;
        }
        $results = [];
        foreach (SystemLanguages::i()->getPossibleLanguageCodes() as $language_code) {
            $control_name = $this->getTranslateNameByLanguageCode($language_code);
            $value = $this->getControlData($control_name);
            if (!empty($value)) {
                $result = SystemLanguages::i()->addTranslate($hint_id, $value, $language_code);
                if (!$result) {
                    $this->addError($control_name,
                        'Перевод на "'. SystemLanguages::i()->getLanguageName($language_code).'" не сохранился.');
                    $result[] = false;
                }
            }
        }
        return empty($results);
    }
}