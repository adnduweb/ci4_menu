<?php

namespace Adnduweb\Ci4_menu\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_menu\Entities\Menu;

class MenuModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    protected $table              = 'menus';
    protected $tableLang          = 'menus_langs';
    protected $with               = ['menus_langs'];
    protected $without            = [];
    protected $primaryKey         = 'id_menu';
    protected $primaryKeyLang     = 'menu_id';
    protected $returnType         = Menu::class;
    protected $localizeFile       = 'Adnduweb\Ci4_menu\Models\MenuModel';
    protected $useSoftDeletes     = false;
    protected $allowedFields      = ['menu_main_id', 'active', 'depth', 'left', 'right', 'id_parent', 'id_module', 'id_item_module', 'icon'];
    protected $useTimestamps      = true;
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function __construct()
    {
        parent::__construct();
        $this->menus       = $this->db->table('menus');
        $this->menus_mains = $this->db->table('menus_mains');
        $this->pages       = $this->db->table('pages');
    }


    public function getMenuAdmin(int $menu_main_id)
    {
        $this->menus->select();
        $this->menus->select('created_at as date_create_at');
        $this->menus->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id');
        $this->menus->where(['menu_main_id' => $menu_main_id, 'id_lang' =>  1]);
        $this->menus->orderBy('left', 'ACS');
        $groupsRow = $this->menus->get()->getResult();
        //echo $this->menus->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->menus->select();
        $this->menus->select('created_at as date_create_at');
        $this->menus->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id_menu');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->menus->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('switchlanguage')->getIdLocale());
            $this->menus->limit(0, $page);
        } else {
            $this->menus->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->menus->limit($perpage, $page);
        }
        $this->menus->orderBy($sort['field'] . ' ' . $sort['sort']);
        $groupsRow = $this->menus->get()->getResult();
        //echo $this->menus->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->menus->select($this->table . '.' . $this->primaryKey);
        $this->menus->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id_menu');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->menus->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('switchlanguage')->getIdLocale());
        } else {
            $this->menus->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
        }

        $this->menus->orderBy($sort['field'] . ' ' . $sort['sort']);

        $menu = $this->menus->get();
        //echo $this->menus->getCompiledSelect(); exit;
        return $menu->getResult();
    }

    public function getMenusMains()
    {
        return $this->menus_mains->orderBy('id', 'ACS')->get()->getResult();
    }

    public function getMenuMain(int $menu_main_id)
    {
        return $this->menus_mains->where('id', $menu_main_id)->get()->getRow();
    }

    public function deleteItem(int $menu_main_id)
    {
        return $this->db->table('menus')->delete(['menu_main_id' => $menu_main_id]);
    }

    public function getAllMenuFront(int $menu_main_id, int $id_lang)
    {
        $this->menus->select($this->table . '.*, ' .  $this->tableLang . '.*');
        $this->menus->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.menu_id');
        $this->menus->where('menu_main_id= ' . $menu_main_id . ' AND  ' . $this->tableLang . '.id_lang = ' . $id_lang);
        $this->menus->orderBy('left ASC');
        $menuResult = $this->menus->get()->getResult();
        $arrayId = [];
        foreach ($menuResult as $result) {
            $arrayId[$result->{$this->primaryKey}] = $result;
        }

        if (!empty($menuResult)) {
            $i = 0;
            foreach ($menuResult as &$menu) {
                $this->pages->select('pages_langs.slug, pages_langs.page_id');
                $this->pages->join('pages_langs', 'pages.id = pages_langs.page_id');
                $this->pages->where('pages_langs.id_lang = ' . $id_lang . ' AND pages.id = ' . $menu->id_item_module);
                $pagedetail = $this->pages->get()->getRow();
                if (!empty($pagedetail)) {
                    if (!is_null($menu->id_module)) {
                        if ($menu->id_parent != 0) {
                            $slugParent  = ($arrayId[$menu->id_parent]->slug != "#") ? $arrayId[$menu->id_parent]->slug : '';
                            $menu->slug = $slugParent . '/' . $pagedetail->slug;
                            $menu->id = $pagedetail->page_id;
                        } else {
                            $menu->slug = $pagedetail->slug;
                            $menu->id = $pagedetail->page_id;
                        }
                    } else {
                        $menu->slug = $menu->slug;
                    }
                }
                $i++;
            }
        }
        return $menuResult;
    }
}
