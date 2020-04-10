<?php

namespace Adnduweb\Ci4_menu\Entities;

use CodeIgniter\Entity;

class Menu extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    protected $table      = 'menus';
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

    public function getNameLang(int $id_lang)
    {
        // print_r($this->setmenusLangs()); exit;
        return $this->setmenusLangs()['menus_langs'][$id_lang]->name ?? null;
    }

    public function getSlug()
    {
        // print_r($this->setmenusLangs()); exit;
        return $this->attributes['slug'] ?? null;
    }

    public function setmenusLangs()
    {
        if (!empty($this->menus_langs)) {
            // unset($this->attributes[$this->tableLang][0]);
            $i = 0;
            foreach ($this->menus_langs as $lang) {
                $this->attributes[$this->tableLang][$lang->id_lang] = $lang;
                $i++;
            }
            unset($this->attributes[$this->tableLang][0]);
        }

        return $this->attributes;
    }


    public function _prepareLang()
    {
        $lang = [];
        if (!empty($this->id)) {
            foreach ($this->menus_langs as $tabs_lang) {
                $lang[$tabs_lang->id_lang] = $tabs_lang;
            }
        }
        return $lang;
    }

    public function saveLang(array $data, int $key)
    {
        //print_r($data);
        $db      = \Config\Database::connect();
        $builder = $db->table($this->tableLang);
        // print_r($data);
        // exit;
        foreach ($data as $k => $v) {
            $this->tableLang =  $builder->where(['id_lang' => $k, 'menu_id' => $key])->get()->getRow();
            // print_r($this->tableLang);
            if (empty($this->tableLang)) {
                $data = [
                    'menu_id' => $key,
                    'id_lang' => $k,
                    'name'    => $v['name']
                ];
                // Create the new participant
                $builder->insert($data);
            } else {
                $data = [
                    'menu_id' => $this->tableLang->menu_id,
                    'id_lang' => $this->tableLang->id_lang,
                    'name'    => $v['name']
                ];
                //print_r($data);
                $builder->set($data);
                $builder->where(['menu_id' => $this->tableLang->menu_id, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }
}
