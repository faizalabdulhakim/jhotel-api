<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Users extends CI_Migration
{
	public function up()
	{
		// name, email, password, role, created_at, updated_at

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'role' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			)
		));

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_field('created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
		$this->dbforge->add_field('updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->dbforge->create_table('users');
	}

	public function down()
	{
		$this->dbforge->drop_table('users');
	}
}
