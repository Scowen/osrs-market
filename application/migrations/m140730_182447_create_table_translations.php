<?php

class m140730_182447_create_table_translations extends CDbMigration
{
	public function up()
	{
		$this->createTable(
            '{{message}}',
            array(
                // Entities.
                'id'            => 'pk                                      COMMENT ""',
                'category'      => 'VARCHAR(32)     NOT NULL                COMMENT ""',
                'message'       => 'text            NOT NULL                COMMENT ""',
            ),
            implode(' ', array(
                'ENGINE          = InnoDB',
                'DEFAULT CHARSET = utf8',
                'COLLATE         = utf8_general_ci',
                'COMMENT         = ""',
                'AUTO_INCREMENT  = 1',
            ))
        );
        $this->createTable(
            '{{translation}}',
            array(
                // Entities.
                'id'            => 'int             NOT NULL                COMMENT ""',
                'language'      => 'VARCHAR(16)     NOT NULL                COMMENT ""',
                'translation'   => 'text            NOT NULL                COMMENT ""',
            ),
            implode(' ', array(
                'ENGINE          = InnoDB',
                'DEFAULT CHARSET = utf8',
                'COLLATE         = utf8_general_ci',
                'COMMENT         = ""',
                'AUTO_INCREMENT  = 1',
            ))
        );
        $this->addPrimaryKey('translation_pk', '{{translation}}', 'id, language');
        $this->addForeignKey('translation_fk_id', '{{translation}}', 'id', '{{message}}', 'id');

	}

	public function down()
	{
		$this->dropTable('{{translation}}');
    	$this->dropTable('{{message}}');
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