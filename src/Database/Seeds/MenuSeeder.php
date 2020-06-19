<?php

namespace Adnduweb\Ci4_menu\Database\Seeds;

use Adnduweb\Ci4_menu\Models\MenuModel;

class MenuSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {

        $db = \Config\Database::connect();

        //Menu
        $rowsItem = [
            'id'         => 1,
            'name'       => 'main menu',
            'handle'     => 'main_menu',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('menus_mains')->insert($rowsItem);
        //$menu_main_id = $db->insertID();

        $rows = [
            'id_menu'        => 1,
            'menu_main_id'   => 1,
            'id_parent'      => 0,
            'depth'          => 1,
            'left'           => 2,
            'right'          => 3,
            'position'       => 1,
            'id_module'      => null,
            'id_item_module' => null,
            'active'         => 1,
            'icon'           => '',
        ];
        $db->table('menus')->insert($rows);

        $rowsLang =  [
            'menu_id' => 1,
            'id_lang' => 1,
            'name'    => 'Page d\'accueil',
            'slug'    => '/'
        ];
        $db->table('menus_langs')->insert($rowsLang);


        $rowsTabs = [
            [
                'id_parent'         => 17,
                'depth'             => 2,
                'left'              => 33,
                'right'             => 34,
                'position'          => 1,
                'section'           => 0,
                'module'            => 'Adnduweb\Ci4_menu',
                'class_name'        => 'AdminMenus',
                'active'            =>  1,
                'icon'              => '',
                'slug'             => 'menus/1',
                'name_controller'       => ''
            ],
        ];

        $rowsTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'menu',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'menu',
            ],
        ];
        $db = \Config\Database::connect();
        foreach ($rowsTabs as $row) {
            $tab = $db->table('tabs')->where('class_name', $row['class_name'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tab)) {
                // No setting - add the row
                $db->table('tabs')->insert($row);
                $newInsert = $db->insertID();
                $i = 0;
                foreach ($rowsTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsert;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }
        }

        /**
         *
         * Gestion des permissions
         */
        $rowsPermissionsMenu = [
            [
                'name'              => 'Menus::views',
                'description'       => 'Voir les Menu',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Menus::create',
                'description'       => 'Créer des Menu',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Menus::edit',
                'description'       => 'Modifier les Menu',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Menus::delete',
                'description'       => 'Supprimer des Menu',
                'is_natif'          => '0',
            ]
        ];

        // On insére le role par default au user
        foreach ($rowsPermissionsMenu as $row) {
            $tabRow =  $db->table('auth_permissions')->where(['name' => $row['name']])->get()->getRow();
            if (empty($tabRow)) {
                // No langue - add the row
                $db->table('auth_permissions')->insert($row);
            }
        }

        //Gestion des module
        $rowsModulePages = [
            'name'       => 'menu',
            'namespace'  => 'Adnduweb\Ci4_menu',
            'active'     => 1,
            'version'    => '1.1.3',
            'created_at' =>  date('Y-m-d H:i:s')
        ];

        $tabRow =  $db->table('modules')->where(['name' => $rowsModulePages['name']])->get()->getRow();
        if (empty($tabRow)) {
            // No langue - add the row
            $db->table('modules')->insert($rowsModulePages);
        }
    }
}
