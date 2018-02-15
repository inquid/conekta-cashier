<?php


class AddConektaCashierColumns extends \yii\db\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%conekta_cashier_table}}', [
            'id' => $this->primaryKey(),
            'conekta_active' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'conekta_id' => $this->string(250),
            'conekta_subscription' => $this->string(250),
            'conekta_plan' => $this->string(35),
            'card_type' => $this->string(30),
            'last_four' => $this->string(4),
            'trial_ends_at' => $this->timestamp(),
            'subscription_ends_at' => $this->timestamp()
         ], $tableOptions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('{{%conekta_cashier_table}}');
    }

}
