<?php

class m140527_162438_create_table_races extends CDbMigration
{
	public function up()
	{
		$this->createTable(
            '{{races}}',
            array(
                'id'            => 'pk                  ',
                'name'          => 'VARCHAR(128)    NULL',
                'start'         => 'INT             NULL',
                'end'           => 'INT             NULL',
                'winner'        => 'INT             NULL',
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
		echo "m140527_162438_create_table_races does not support migration down.\n";
		return false;
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