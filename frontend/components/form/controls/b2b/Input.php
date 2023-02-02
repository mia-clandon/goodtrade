<?

namespace frontend\components\form\controls\b2b;

use frontend\components\form\controls\Input as BaseInput;

/**
 * Class Input
 * @package frontend\components\form\controls\b2b
 * @author yerganat
 */
class Input extends BaseInput {

    protected function beforeRender() {
        parent::beforeRender();
        $this->setTemplateName('b2b/input');
    }
}