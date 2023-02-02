<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Checkbox as BaseCheckbox;

/**
 * Class Input
 * @package frontend\components\form\controls\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class Checkbox extends BaseCheckbox {

    protected function beforeRender() {
        parent::beforeRender();
        $this->addClass('checkbox__input');
        $this->setTemplateName('b2b/checkbox');
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'control' => $control,
        ]);
    }
}