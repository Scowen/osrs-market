<?php

class m140730_181832_create_table_item_history extends CDbMigration
{
	public function up()
	{
		$this->createTable(
            '{{item_history}}',
            array(
                'id'            => 'pk                  ',
                'item'          => 'INT 	     NOT NULL',
                'offers'        => 'INT 	         NULL',
                'quantity'      => 'INT 	         NULL',
                'average'       => 'DOUBLE           NULL',
                'high'          => 'DOUBLE           NULL',
                'low'           => 'DOUBLE           NULL',
                'updated'       => 'INT              NULL',
                'created'       => 'INT              NULL',
            ),
            implode(' ', array(
                'ENGINE          = InnoDB',
                'DEFAULT CHARSET = utf8',
                'COLLATE         = utf8_general_ci',
                'AUTO_INCREMENT  = 1',
            ))
        );

        $this->addForeignKey('fk_itemh_item_items_id', '{{item_history}}', 'item', '{{items}}', 'id');
	}

	public function down()
	{
		$this->dropTable("{{item_history}}");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}