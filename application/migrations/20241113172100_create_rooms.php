<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Rooms extends CI_Migration
{
	public function up()
	{


		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'price' => array(
				'type' => 'INT',
			),
			'description' => array(
				'type' => 'TEXT',
			),
			'availability' => array(
				'type' => 'BOOLEAN',

			),
		));

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_field('created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
		$this->dbforge->add_field('updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

		$this->dbforge->create_table('rooms');
	}

	public function down()
	{
		$this->dbforge->drop_table('rooms');
	}
}
