<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Control;
use common\libs\form\components\Input;

use yii\helpers\ArrayHelper;

/**
 * Контрол диапазона объема производства.
 * Class CapacityRange
 * @package frontend\components\form\controls\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class CapacityRange extends Control {

    const FROM_INPUT    = 'from';
    const TO_INPUT      = 'to';

    protected $template_name = 'b2b/capacities';

    /** @var Input */
    private $range_from_input;
    /** @var Input */
    private $range_to_input;

    public function setValue($value) {
        if (is_array($value)) {

            $from = ArrayHelper::getValue($value, self::FROM_INPUT, 0);
            $to = ArrayHelper::getValue($value, self::TO_INPUT, 0);

            $this->getRangeFromInput()->setValue((float)$from);
            $this->getRangeToInput()->setValue((float)$to);
        }
        return parent::setValue($value);
    }

    /**
     * Возвращает все данные контрола в формате:
     * [
     *    'from' => n
     *    'to' => n
     * ]
     * @return array
     */
    public function getValue() {
        $value =  parent::getValue();
        $from = $value[self::FROM_INPUT] ?? 0;
        $to = $value[self::TO_INPUT] ?? 0;
        return [
            self::FROM_INPUT => (float)$from,
            self::TO_INPUT => (float)$to,
        ];
    }

    /**
     * @return Input
     */
    private function getRangeFromInput(): Input {
        if (null === $this->range_from_input) {
            $this->range_from_input = (new Input())
                ->setName($this->getName(). '['.self::FROM_INPUT.']')
                ->setType(Input::TYPE_NUMBER)
            ;
        }
        return $this->range_from_input;
    }

    /**
     * @return Input
     */
    private function getRangeToInput(): Input {
        if (null === $this->range_to_input) {
            $this->range_to_input = (new Input())
                ->setName($this->getName(). '['.self::TO_INPUT.']')
                ->setType(Input::TYPE_NUMBER)
            ;
        }
        return $this->range_to_input;
    }

    public function render(): string {
        return $this->renderTemplate([
            'from_input'    => $this->getRangeFromInput()->render(),
            'to_input'      => $this->getRangeToInput()->render(),
            'title'         => $this->getTitle(),
        ]);
    }
}