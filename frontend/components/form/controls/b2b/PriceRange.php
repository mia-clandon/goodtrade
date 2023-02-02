<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Control;
use common\libs\form\components\Input;
use common\libs\form\components\Checkbox;

use yii\helpers\ArrayHelper;

/**
 * Контрол диапазона цен + выбор "с НДС/без".
 * Class InputRange
 * @package frontend\components\form\controls\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class PriceRange extends Control {

    const FROM_INPUT    = 'from';
    const TO_INPUT      = 'to';
    const WITH_VAT      = 'with_vat';

    protected $template_name = 'b2b/price-range';

    /** @var Input */
    private $range_from_input;
    /** @var Input */
    private $range_to_input;
    /** @var Checkbox */
    private $vat_checkbox;

    public function setValue($value) {
        if (is_array($value)) {

            $from = (float)ArrayHelper::getValue($value, self::FROM_INPUT, 0);
            $to = (float)ArrayHelper::getValue($value, self::TO_INPUT, 0);
            $with_vat = (bool)ArrayHelper::getValue($value, self::WITH_VAT, false);

            $this->getRangeFromInput()->setValue($from);
            $this->getRangeToInput()->setValue($to);
            $this->getVatCheckbox()->setChecked((bool)$with_vat);
        }
        return parent::setValue($value);
    }

    /**
     * Возвращает все данные контрола в формате:
     * [
     *    'from' => n
     *    'to' => n
     *    'with_vat' => bool
     * ]
     * @return array
     */
    public function getValue() {
        $value =  parent::getValue();
        $from = $value[self::FROM_INPUT] ?? 0;
        $to = $value[self::TO_INPUT] ?? 0;
        $with_vat = $value[self::WITH_VAT] ?? false;
        return [
            self::FROM_INPUT => (float)$from,
            self::TO_INPUT => (float)$to,
            self::WITH_VAT => (bool)$with_vat,
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

    /**
     * @return Checkbox
     */
    private function getVatCheckbox(): Checkbox {
        if (null === $this->vat_checkbox) {
            $this->vat_checkbox = (new Checkbox())
                ->setName($this->getName(). '['.self::WITH_VAT.']')
                ->addClass('checkbox__input')
            ;
        }
        return $this->vat_checkbox;
    }

    public function render(): string {
        return $this->renderTemplate([
            'from_input'    => $this->getRangeFromInput()->render(),
            'to_input'      => $this->getRangeToInput()->render(),
            'vat_checkbox'  => $this->getVatCheckbox()->render(),
            'title'         => $this->getTitle(),
        ]);
    }
}