<?php

namespace backend\forms\user;

use backend\components\form\controls\Button;
use backend\components\form\controls\Checkbox;
use backend\components\form\Form;
use common\libs\helpers\Permission;
use common\models\User;

/**
 * Class Permissions
 * @package backend\forms\user
 * @author Артём Широких kowapssupport@gmail.com
 */
class Permissions extends Form {

    /** @var User */
    private $user;

    public function initControls(): void {
        parent::initControls();

        foreach (Permission::i()->getRoleNames() as $permission => $name) {
            $control = new Checkbox();
            $control->setName($this->getPermissionControlName($permission));
            $control->setTitle($name);
            $control->setChecked($this->isChecked($permission));
            $this->registerControl($control);
        }

        $button_control = (new Button())
            ->setName('submit')
            ->setContent('Сохранить')
            ->setType(Button::TYPE_SUBMIT)
            ->setButtonType(Button::BTN_TYPE_PRIMARY);
        $this->registerControl($button_control);
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     * @return self
     */
    public function setUser(User $user): self {
        $this->user = $user;
        return $this;
    }

    /**
     * @param string $permission
     * @return bool
     */
    private function isChecked(string $permission): bool {
        $roles = Permission::i()->getUserRoles($this->getUser());
        return \in_array($permission, $roles, true);
    }

    /**
     * @param string $permission
     * @return string
     */
    private function getPermissionControlName(string $permission): string {
        return 'permission['.$permission.']';
    }

    /**
     * @return array
     */
    private function getCheckedPermissions(): array {
        $permissions = [];
        foreach (\Yii::$app->request->post('permission') as $permission_name => $permission_value) {
            if ($permission_value === "1" || (bool)$permission_value) {
                $permissions[] = $permission_name;
            }
        }
        return $permissions;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save(): bool {
        return Permission::i()
            ->assignRoles($this->getUser(), $this->getCheckedPermissions());
    }
}