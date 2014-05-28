<?php

class m140528_154932_add_column_races_extra extends CDbMigration
{
	public function up()
	{
		$this->addColumn('{{races}}', 'extra', 'INT NULL');
	}

	public function down()
	{
		echo "m140528_154932_add_column_races_extra does not support migration down.\n";
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