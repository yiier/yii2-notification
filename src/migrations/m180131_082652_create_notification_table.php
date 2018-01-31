<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification`.
 */
class m180131_082652_create_notification_table extends Migration
{
    /**
     * @var string 通知
     */
    public $tableName = '{{%notification}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'type' => $this->string(20)->notNull(),
            'from_user_id' => $this->integer()->defaultValue(null),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string()->defaultValue(null),
            'content' => $this->text()->notNull(),
            'model' => $this->string(20)->defaultValue(null),
            'model_id' => $this->integer()->defaultValue(null),
            'status' => $this->smallInteger(2)->defaultValue(0),
            'created_at' => $this->integer()->defaultValue(null),
        ]);

        $this->addCommentOnTable($this->tableName, '通知表');
        $this->createIndex('fk_user_id', $this->tableName, ['user_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
