<?php

namespace Adnduweb\Ci4_menu\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_menu\Entities\MenuMain;

class MenuMainModel extends Model
{
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    protected $table           = 'menus_mains';
    protected $primaryKey      = 'id';
    protected $returnType      = MenuMain::class;
    protected $localizeFile    = 'Adnduweb\Ci4_menu\Models\MenuMainModel';
    protected $useSoftDeletes  = false;
    protected $allowedFields   = ['name', 'handle'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'name'            => 'required'
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function __construct()
    {
        parent::__construct();
        $this->menus_mains = $this->db->table('menus_mains');
    }
}
