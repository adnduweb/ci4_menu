<?php

namespace Adnduweb\Ci4_menu\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_menu\Entities\Menu;

class MenusModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    protected $table = 'menus';
    protected $tableLang = 'menus_langs';
    protected $with = ['menus_langs'];
    protected $without = [];
    protected $primaryKey = 'id';
    protected $returnType = Menu::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id_menu_item', 'active', 'depth', 'left', 'right', 'id_parent', 'slug', 'id_module', 'id_item_module', 'icon'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'slug'            => 'required'
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function __construct()
    {
        parent::__construct();
        $this->menu = $this->db->table('menus');
        $this->page = $this->db->table('pages');
    }

    /* @Todo */
    //Voir le module de relation @Tatter
    public function tmpReset()
    {
    }


    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->menu->select();
        $this->menu->select('created_at as date_create_at');
        $this->menu->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id_menu');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->menu->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->menu->limit(0, $page);
        } else {
            $this->menu->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->menu->limit($perpage, $page);
        }
        $this->menu->orderBy($sort['field'] . ' ' . $sort['sort']);
        $groupsRow = $this->menu->get()->getResult();
        //echo $this->menu->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->menu->select($this->table . '.' . $this->primaryKey);
        $this->menu->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id_menu');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->menu->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->menu->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->menu->orderBy($sort['field'] . ' ' . $sort['sort']);

        $menus = $this->menu->get();
        //echo $this->menu->getCompiledSelect(); exit;
        return $menus->getResult();
    }

    public function getMenusItems()
    {
        return $this->db->table('menus_items')->orderBy('id_menu_item', 'ACS')->get()->getResult();
    }

    public function getMenusItem(int $id_menu_item)
    {
        return $this->db->table('menus_items')->where('id_menu_item', $id_menu_item)->get()->getRow();
    }

    public function deleteItem(int $id_menu_item)
    {
        return $this->db->table('menus')->delete(['id_menu_item' => $id_menu_item]);
    }

    public function getAllMenuFront(int $id_menu_item, int $id_lang)
    {
        $this->menu->select($this->table . '.*, ' .  $this->tableLang . '.*');
        $this->menu->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id');
        // $this->menu->join('pages', 'pages.id_page = ' . $this->table . '.id_item_module');
        // $this->menu->join('pages_langs', 'pages.id_page = pages_langs.id_page');
        //$this->menu->where('id_menu_item= ' . $id_menu_item . ' AND  ' . $this->tableLang . '.id_lang = ' . $id_lang . ' AND pages_langs.id_lang = ' . $id_lang);
        $this->menu->where('id_menu_item= ' . $id_menu_item . ' AND  ' . $this->tableLang . '.id_lang = ' . $id_lang);
        $this->menu->orderBy('left ASC');
        $menuResult = $this->menu->get()->getResult();
        $arrayId = [];
        foreach ($menuResult as $result) {
            $arrayId[$result->id] = $result;
        }

        if (!empty($menuResult)) {
            $i = 0;
            foreach ($menuResult as &$menu) {
                $this->page->select('pages_langs.slug, pages_langs.id_page');
                $this->page->join('pages_langs', 'pages.id_page = pages_langs.id_page');
                $this->page->where('pages_langs.id_lang = ' . $id_lang . ' AND pages.id_page = ' . $menu->id_item_module);
                $pagedetail = $this->page->get()->getRow();
                if (!empty($pagedetail)) {
                    if (!is_null($menu->id_module)) {
                        if ($menu->id_parent != 0) {
                            $menu->slug = $arrayId[$menu->id_parent]->slug . '/' . $pagedetail->slug;
                            $menu->id_page = $pagedetail->id_page;
                        } else {
                            $menu->slug = $pagedetail->slug;
                            $menu->id_page = $pagedetail->id_page;
                        }
                    } else {
                        $menu->slug = $menu->slug;
                    }
                }
                $i++;
            }
        }

        //print_r($arrayId);
        // print_r($menuResult);
        // exit;
        //echo $this->menu->getCompiledSelect(); exit;
        return $menuResult;
    }
}
