<?php

namespace Adnduweb\Ci4_menu\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_menu\Entities\MenuItem;

class MenusItemsModel extends Model
{
    use \Spreadaurora\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    protected $table = 'menus_items';
    protected $primaryKey = 'id_menu_item';
    protected $returnType = MenuItem::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'handle'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'name'            => 'required'
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function __construct()
    {
        parent::__construct();
        $this->menu = $this->db->table('menus_items');
    }

    /* @Todo */
    //Voir le module de relation @Tatter
    public function tmpReset()
    {
    }
}
