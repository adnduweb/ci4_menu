<?php

namespace Spreadaurora\ci4_menu\Database\Seeds;

use Spreadaurora\ci4_menu\Models\MenusModel;

class Menuseeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
 
        $rowsTabs = [
            [
                'id_parent'         => 17,
                'depth'             => 2,
                'left'              => 33,
                'right'             => 34,
                'position'          => 1,
                'section'           => 0,
                'module'            => 'Spreadaurora\ci4_menu',
                'class_name'        => 'AdminMenus',
                'active'            =>  1,
                'icon'              => '',
                'slug'             => 'menus',
                'name_controller'       => ''
            ],
        ];

        $rowsTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'menus',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'menus',
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
                        $rowLang['menu_id']   = $newInsert;
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
        $rowsPermissionsMenus = [
            [
                'name'              => 'Menus::views',
                'description'       => 'Voir les Menus',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Menus::create',
                'description'       => 'Créer des Menus',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Menus::edit',
                'description'       => 'Modifier les Menus',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Menus::delete',
                'description'       => 'Supprimer des Menus',
                'is_natif'          => '0',
            ]
        ];

         // On insére le role par default au user
         foreach ($rowsPermissionsMenus as $row) {
            $tabRow =  $db->table('auth_permissions')->where(['name' => $row['name']])->get()->getRow();
            if (empty($tabRow)) {
                // No langue - add the row
                $db->table('auth_permissions')->insert($row);
            }
        }
    }
}
