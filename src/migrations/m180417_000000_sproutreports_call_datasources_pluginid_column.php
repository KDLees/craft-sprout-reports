<?php

namespace barrelstrength\sproutreports\migrations;

use craft\db\Migration;
use barrelstrength\sproutbase\app\reports\migrations\m180417_000000_sproutreports_datasources_pluginid_column as SproutReportsPluginId;

/**
 * m180417_000000_sproutreports_call_datasources_pluginid_column migration.
 */
class m180417_000000_sproutreports_call_datasources_pluginid_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $migration = new SproutReportsPluginId();

        ob_start();
        $migration->safeUp();
        ob_end_clean();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180417_000000_sproutreports_call_datasources_pluginid_column cannot be reverted.\n";
        return false;
    }
}
