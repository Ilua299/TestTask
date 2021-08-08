<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%college}}`.
 */
class m210807_134830_create_college_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%college}}', [
            'id' => $this->primaryKey(),
            'college_id' => $this->integer()->uniqueKey(college_id),
            'college_name' => $this->text(),
            'address' => $this->text(),
            'phone' => $this->text(),
            'site' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%college}}');
    }
}
