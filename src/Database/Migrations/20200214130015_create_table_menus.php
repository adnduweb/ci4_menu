<?php

namespace Spreadaurora\ci4_menu\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_menus extends Migration
{
	public function up()
	{

		$fields = [
			'id_menu_item' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'name'         => ['type' => 'VARCHAR', 'constraint' => 255],
			'handle'       => ['type' => 'VARCHAR', 'constraint' => 255],
			'created_at'   => ['type' => 'DATETIME', 'null' => true],
			'updated_at'   => ['type' => 'DATETIME', 'null' => true],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id_menu_item', true);
		$this->forge->addKey('created_at');
		$this->forge->addKey('updated_at');
		$this->forge->createTable('menus_items');

		$fields = [
			'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'id_menu_item'   => ['type' => 'INT', 'constraint' => 11],
			'id_parent'      => ['type' => 'INT', 'constraint' => 11],
			'depth'          => ['type' => 'INT', 'constraint' => 11],
			'left'           => ['type' => 'INT', 'constraint' => 11],
			'right'          => ['type' => 'INT', 'constraint' => 11],
			'position'       => ['type' => 'INT', 'constraint' => 11],
			'id_module'      => ['type' => 'INT', 'null' => TRUE],
			'id_item_module' => ['type' => 'INT', 'null' => TRUE],
			'active'         => ['type' => 'INT', 'constraint' => 11, 'null' => true],
			'icon'           => ['type' => 'TEXT', 'null' => true, 'null' => true],
			'slug'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'created_at'     => ['type' => 'DATETIME', 'null' => true],
			'updated_at'     => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->addKey('created_at');
		$this->forge->addKey('updated_at');
		$this->forge->addKey('deleted_at');
		$this->forge->createTable('menus');


		$fields = [
			'menu_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'id_lang'      => ['type' => 'INT', 'constraint' => 11],
			'name'         => ['type' => 'VARCHAR', 'constraint' => 255]
		];

		$this->forge->addField($fields);
		// $this->forge->addKey(['id_item', 'id_lang'], false, true);
		$this->forge->addKey('id_item');
		$this->forge->addKey('id_lang');
		$this->forge->addForeignKey('menu_id', 'menus', 'id', false, 'CASCADE');
		$this->forge->createTable('menus_langs', true);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('menus');
		$this->forge->dropTable('menus_langs');
		$this->forge->dropTable('menus_items');
	}
}
