<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Input as BaseInput;

/**
 * Class RegionInput
 * @package frontend\components\form\controls\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class RegionInput extends BaseInput {

    protected $template_name = 'b2b/region-input';

    //todo: поисковик.

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}