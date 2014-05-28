<?php

class m140528_134803_insert_races extends CDbMigration
{
	public function up()
	{
		$this->insert('{{races}}', array(
            'name'    => 'First',
            'start'   => 1403443800,
            'end'     => 1403445900,
            'created' => time(),
        ));

		$this->insert('{{races}}', array(
            'name'    => 'Second',
            'start'   => 1403445900,
            'end'     => 1403448300,
            'created' => time(),
        ));

		$this->insert('{{races}}', array(
            'name'    => 'Third',
            'start'   => 1403448300,
            'end'     => 1403450700,
            'created' => time(),
        ));

		$this->insert('{{races}}', array(
            'name'    => 'Fourth',
            'start'   => 1403450700,
            'end'     => 1403452800,
            'created' => time(),
        ));	

		$this->insert('{{races}}', array(
            'name'    => 'Fitfh',
            'start'   => 1403452800,
            'end'     => 1403454900,
            'created' => time(),
        ));
			
		$this->insert('{{races}}', array(
            'name'    => 'Sixth',
            'start'   => 1403454900,
            'start'   => 1403457300,
            'created' => time(),
        ));
	}

	public function down()
	{
		echo "m140528_134803_insert_races does not support migration down.\n";
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