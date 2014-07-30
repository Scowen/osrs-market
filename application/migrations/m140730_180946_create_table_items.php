<?php

class m140730_180946_create_table_items extends CDbMigration
{
	public function up()
	{
		$this->createTable(
            '{{items}}',
            array(
                'id'            => 'pk                  ',
                'name'          => 'VARCHAR(128)     NULL',
                'zybez_id'      => 'INT              NULL',
                'zybez_search'  => 'VARCHAR(256) NOT NULL',
                'image'         => 'VARCHAR(512)     NULL',
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

	}

	public function down()
	{
		$this->dropTable("{{items}}");
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