<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%allcollegies}}`.
 */
class m210807_134617_create_allcollegies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%allcollegies}}', [
            'id' => $this->primaryKey(),
            'college_id' => $this->integer()->uniqueKey(college_id),
            'image_src' => $this->text(),
            'college_name' => $this->text(),
            'city' => $this->text(),
            'state' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%allcollegies}}');
    }
}
