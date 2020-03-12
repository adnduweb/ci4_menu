<?php

namespace Spreadaurora\ci4_menus\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_menus extends Migration
{
	public function up()
	{

        $fields = [
			'id_menu_item'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'created_at'      => ['type' => 'DATETIME', 'null' => true],
			'updated_at'      => ['type' => 'DATETIME', 'null' => true],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id_menu_item', true);
		$this->forge->addKey('created_at');
		$this->forge->addKey('updated_at');
        $this->forge->createTable('menus_ttems');
        
		$fields = [
            'id_menu'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_menu_item'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'id_parent'       => ['type' => 'INT', 'constraint' => 11],
			'depth'           => ['type' => 'INT', 'constraint' => 11],
			'left'            => ['type' => 'INT', 'constraint' => 11],
			'right'           => ['type' => 'INT', 'constraint' => 11],
			'position'        => ['type' => 'INT', 'constraint' => 11],
			'section'         => ['type' => 'INT', 'constraint' => 11],
			'module'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
			'class_name'      => ['type' => 'VARCHAR', 'constraint' => 255],
			'active'          => ['type' => 'INT', 'constraint' => 11],
			'icon'            => ['type' => 'TEXT'],
			'slug'            => ['type' => 'VARCHAR', 'constraint' => 255],
			'name_controller' => ['type' => 'VARCHAR', 'constraint' => 255],
			'created_at'      => ['type' => 'DATETIME', 'null' => true],
			'updated_at'      => ['type' => 'DATETIME', 'null' => true],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id_menu', true);
		$this->forge->addKey('created_at');
		$this->forge->addKey('updated_at');
		$this->forge->createTable('menus');


		$fields = [
			'menu_id_menu' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'id_lang'      => ['type' => 'INT', 'constraint' => 11],
			'name'         => ['type' => 'VARCHAR', 'constraint' => 255]
		];

		$this->forge->addField($fields);
		// $this->forge->addKey(['id_item', 'id_lang'], false, true);
		$this->forge->addKey('id_item');
		$this->forge->addKey('id_lang');
		$this->forge->addForeignKey('menu_id_menu', 'menus', 'id_menu', false, 'CASCADE');
		$this->forge->createTable('menus_langs', true);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('menus');
		$this->forge->dropTable('menus_langs');
	}
}
