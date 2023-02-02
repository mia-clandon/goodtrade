<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M210801181759FixUsersTable
 */
class M210801181759FixUsersTable extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE COLUMN `username` `username` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci' AFTER `id`,
            CHANGE COLUMN `auth_key` `auth_key` VARCHAR(32) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci' AFTER `phone_real`,
            CHANGE COLUMN `password_hash` `password_hash` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci' AFTER `auth_key`,
            CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci' AFTER `password_reset_token`,
            CHANGE COLUMN `auth_token` `auth_token` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `status`,
            CHANGE COLUMN `created_at` `created_at` INT(11) NOT NULL DEFAULT '0' AFTER `temp_bin`,
            CHANGE COLUMN `updated_at` `updated_at` INT(11) NOT NULL DEFAULT '0' AFTER `created_at`;
        ");
    }

    public function safeDown(): bool
    {
        echo "M210801181759FixUsersTable cannot be reverted.\n";
        return false;
    }
}
