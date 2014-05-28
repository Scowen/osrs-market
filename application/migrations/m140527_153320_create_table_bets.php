<?php

class m140527_153320_create_table_bets extends CDbMigration
{
	public function up()
	{
		$this->createTable(
            '{{bets}}',
            array(
                'id'            => 'pk                  ',
                'name'          => 'VARCHAR(128)    NULL',
                'race'          => 'INT             NULL',
                'horse'         => 'INT             NULL',
                'quantity'      => 'INT             NULL',
                'created'       => 'INT         NOT NULL',
            ),
            implode(' ', array(
                'ENGINE          = InnoDB',
                'DEFAULT CHARSET = utf8',
                'COLLATE         = utf8_general_ci',
                'AUTO_INCREMENT  = 1',
            ))
        );

	}

	public function down()
	{
		$this->dropTable("{{bets}}");
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