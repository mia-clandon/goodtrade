<?php

namespace common\libs\helpers;

use common\libs\traits\Singleton;
use common\models\User;
use yii\rbac\Role;

/**
 * Class Permission
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class Permission {
    use Singleton;

    #region Роли пользователей.
    public const ROLE_ADMIN         = 'admin';
    public const ROLE_TRANSLATOR    = 'translator';

    #endregion

    /**
     * Возвращает роли пользователей.
     * @return array
     */
    public function getRoleNames(): array {
        return [
            self::ROLE_ADMIN      => 'Администратор',
            self::ROLE_TRANSLATOR => 'Переводчик',
        ];
    }

    /**
     * Группа ролей для доступа к админ панели.
     * @return array
     */
    public function getAdminGroupRoles(): array {
        return [
            self::ROLE_ADMIN,
            self::ROLE_TRANSLATOR,
        ];
    }

    /**
     * @param string $role
     * @return null|string
     */
    public function getRoleName(string $role): ?string {
        return $this->getRoleNames()[$role] ?? null;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool {
        return array_key_exists($role, $this->getRoleNames());
    }

    /**
     * Создаёт новую роль (если необходимо) и присваивает её пользователю (если необходимо).
     * @param User $user
     * @param string $role_name
     * @return bool
     * @throws \Exception
     * todo: возможность добавлять пермишены (суб роли.)
     */
    public function assignRole(User $user, string $role_name): bool {
        if (!$this->hasRole($role_name)) {
            return false;
        }
        /** @var \yii\rbac\ManagerInterface $auth_manager */
        $auth_manager = \Yii::$app->authManager;
        $role = $auth_manager->getRole($role_name);
        if (null === $role) {
            $role = $auth_manager->createRole($role_name);
            $auth_manager->add($role);
            // наследники от admin.
            $admin_role = $auth_manager->getRole(self::ROLE_ADMIN);
            $auth_manager->addChild($admin_role, $role);
        }
        $user_roles_array = $this->getUserRoles($user);
        if (!\in_array($role_name, $user_roles_array, true)) {
            $auth_manager->assign($role, $user->id);
        }
        return true;
    }

    /**
     * @param User $user
     * @param array $role_names
     * @return bool
     * @throws \Exception
     */
    public function assignRoles(User $user, array $role_names): bool {
        /** @var \yii\rbac\ManagerInterface $auth_manager */
        $auth_manager = \Yii::$app->authManager;
        $transaction = \Yii::$app->db->beginTransaction();
        $auth_manager->revokeAll($user->id); // отвязывает предъидущие роли.
        foreach ($role_names as $role_name) {
            $result = $this->assignRole($user, $role_name);
            if (!$result) {
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return true;
    }

    /**
     * @param array $permissions
     * @return bool
     */
    public function can(array $permissions): bool {
        foreach ($permissions as $permission) {
            if (!\array_key_exists($permission, $this->getRoleNames())) {
                continue;
            }
            if (\Yii::$app->getUser()->can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserRoles(User $user): array {
        /** @var \yii\rbac\ManagerInterface $auth_manager */
        $auth_manager = \Yii::$app->authManager;
        $user_roles = $auth_manager->getRolesByUser($user->id);
        $user_roles_array = [];
        /** @var Role $user_role */
        foreach ($user_roles as $user_role) {
            $user_roles_array[] = $user_role->name;
        }
        return $user_roles_array;
    }
}