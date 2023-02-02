<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace console\migrations;

use yii\db\Migration;

ini_set("memory_limit", "1G");
set_time_limit(0);

/**
 * Class M210711232159InitApp
 */
class M210711232159InitApp extends Migration
{
    public function safeUp()
    {
        \Yii::$app->db->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $tables = \Yii::$app->db->getSchema()->getTableNames();
        $this->execute("
            SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
        ");
        foreach ($tables as $table) {
            if ($table === 'migration') {
                continue;
            }
            $this->dropTable($table);
        }
        $this->execute("
            SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
        ");
        $sql = file_get_contents(__DIR__ . '/../../console/migrations/files/init.sql');
        $command = \Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    public function down(): bool
    {
        echo "M210711232159InitApp cannot be reverted.\n";
        return false;
    }
}
