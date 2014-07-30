<?php

class m140730_182559_create_table_user extends CDbMigration
{
	public function up()
	{
		$this->createTable(
            '{{user}}',
            array(
                'id'          => 'pk                    ',
                'username'    => 'VARCHAR(64)   NOT NULL',
                'password'    => 'CHAR(60)      NOT NULL',
                'email'       => 'VARCHAR(128)  NOT NULL',
                'active'      => 'INT(1)        NOT NULL DEFAULT 0',
                'pro'         => 'INT(1)        NOT NULL DEFAULT 0',
                'admin'       => 'INT(3)        NOT NULL DEFAULT 10',
                'created'     => 'INT           NOT NULL',
            ),
            implode(' ', array(
                'ENGINE          = InnoDB',
                'DEFAULT CHARSET = utf8',
                'COLLATE         = utf8_general_ci',
                'COMMENT         = ""',
                'AUTO_INCREMENT  = 1',
            ))
        );
	}

	public function down()
	{
		$this->dropTable("{{user}}");
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