<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Select as BaseSelect;

/**
 * Class Select
 * @package frontend\components\form\controls\b2b
 * @author Артём Широких kowapssupport@gmail.com
 */
class Select extends BaseSelect {

    protected $template_name = 'b2b/select';

    public function beforeRender() {
        parent::beforeRender();
        $this->setAttributes(['data-placeholder-text' => $this->getPlaceholder()]);
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
        ]);
    }
}