<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M210808205439FirmBinFix
 */
class M210808205439FirmBinFix extends Migration
{
    public function up(): void
    {
        $this->execute("
            ALTER TABLE `firm`
        	CHANGE COLUMN `bin` `bin` VARCHAR(255) NULL DEFAULT '' COMMENT 'БИН' COLLATE 'utf8_general_ci' AFTER `legal_address`;
        ");
    }

    public function down(): bool
    {
        echo "M210808205439FirmBinFix cannot be reverted.\n";
        return false;
    }
}
