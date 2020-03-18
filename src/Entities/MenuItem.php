<?php

namespace Spreadaurora\ci4_menu\Entities;

use CodeIgniter\Entity;

class MenuItem extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    protected $table      = 'menus_items';
    protected $tableLang  = 'menus_langs';
    protected $primaryKey = 'id';

    protected $datamap = [];
    /**
     * Define properties that are automatically converted to Time instances.
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    /**
     * Array of field names and the type of value to cast them as
     * when they are accessed.
     */
    protected $casts = [];

    public function getId()
    {
        return $this->{$this->primaryKey} ?? null;
    }
    public function getName()
    {
        return $this->attributes['name'] ?? null;
    }
}
