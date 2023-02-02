<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M210808130911UpdateFirmTable
 */
class M210808130911UpdateFirmTable extends Migration
{
    public function up()
    {
        $this->execute('TRUNCATE TABLE `firm`');
        $this->execute("ALTER TABLE `firm`
	CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '#' FIRST,
	CHANGE COLUMN `status` `status` SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Статус организации' AFTER `id`,
	CHANGE COLUMN `title` `title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Название организации' COLLATE 'utf8_general_ci' AFTER `status`,
	CHANGE COLUMN `image` `image` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci' AFTER `text`,
	CHANGE COLUMN `profile_id` `profile_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Профиль организации' AFTER `image`,
	CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `profile_id`,
	CHANGE COLUMN `is_top` `is_top` SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Топовый продавец ?' AFTER `user_id`,
	CHANGE COLUMN `country_id` `country_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Страна' AFTER `is_top`,
	CHANGE COLUMN `region_id` `region_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Область' AFTER `country_id`,
	CHANGE COLUMN `city_id` `city_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Город' AFTER `region_id`,
	CHANGE COLUMN `bin` `bin` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'БИН' COLLATE 'utf8_general_ci' AFTER `legal_address`,
	CHANGE COLUMN `bank` `bank` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Банк бенефициара' COLLATE 'utf8_general_ci' AFTER `bin`,
	CHANGE COLUMN `bik` `bik` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'БИК' COLLATE 'utf8_general_ci' AFTER `bank`,
	CHANGE COLUMN `iik` `iik` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'ИИК' COLLATE 'utf8_general_ci' AFTER `bik`,
	CHANGE COLUMN `kbe` `kbe` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'КБЕ' COLLATE 'utf8_general_ci' AFTER `iik`,
	CHANGE COLUMN `knp` `knp` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'КНП' COLLATE 'utf8_general_ci' AFTER `kbe`,
	CHANGE COLUMN `created_at` `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `knp`,
	CHANGE COLUMN `updated_at` `updated_at` INT(11) UNSIGNED NOT NULL DEFAULT 0
");
    }

    public function down(): bool
    {
        echo "M210808130911UpdateFirmTable cannot be reverted.\n";
        return false;
    }
}
