<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m251201_162106_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(20),
            'created_at' => $this->integer(),
            'fell_at' => $this->integer()->null(),
            'status' => $this->tinyInteger()->defaultValue(0)
            ->comment("0 = на дереве, 1 = упало, 2 = сгнило"),
            'eaten' => $this->integer()->defaultValue(0)->comment("процент съеденого")
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
