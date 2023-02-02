<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Input as BaseInput;

/**
 * Class SliderInput
 * Слайдер с ползунком (используется к примеру для размера партии товаров)
 * @package frontend\components\form\controls\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class SliderInput extends BaseInput {

    protected $template_name = 'b2b/slider-input';

    /** @var string  */
    private $unit_name = '';

    /** @var float */
    private $max_value = 100;
    /** @var float */
    private $min_value = 0;
    /** @var int - Количество нулей после запятой. */
    private $decimals = 0;

    /**
     * Название единицы измерения.
     * @param string $unit_name
     * @return $this
     */
    public function setUnitName(string $unit_name): self {
        $this->unit_name = $unit_name;
        return $this;
    }

    /**
     * Максимальное значение слайда.
     * @param float $max_value
     * @return $this
     */
    public function setMaxValue(float $max_value): self {
        $this->max_value = $max_value;
        return $this;
    }

    /**
     * Минимальное значение слайда.
     * @param float $min_value
     * @return $this
     */
    public function setMinValue(float $min_value): self {
        $this->min_value = $min_value;
        return $this;
    }

    /**
     * @param int $decimals
     * @return $this
     */
    public function setDecimals(int $decimals): self {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'unit_name'     => $this->unit_name,
            'max_value'     => $this->max_value,
            'min_value'     => $this->min_value,
            'decimals'      => $this->decimals,
            'control'       => $control,
        ]);
    }
}