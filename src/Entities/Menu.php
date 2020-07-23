<?php

namespace Adnduweb\Ci4_menu\Entities;

use CodeIgniter\Entity;

class Menu extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    use \App\Traits\BuilderEntityTrait;
    protected $table      = 'menus';
    protected $tableLang  = 'menus_langs';
    protected $primaryKey = 'id_menu';
    protected $primaryKeyLang = 'menu_id';

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


    public function getSlug()
    {
        // print_r($this->setmenuLangs()); exit;
        return $this->attributes['slug'] ?? null;
    }

    public function setmenuLangs()
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
        if (!empty($this->id_menu)) {
            foreach ($this->menus_langs as $tabs_langs) {
                $lang[$tabs_langs->id_lang] = $tabs_langs;
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
            $this->tableLang =  $builder->where(['id_lang' => $k, $this->primaryKeyLang => $key])->get()->getRow();
            // print_r($this->tableLang);
            if (empty($this->tableLang)) {
                $data = [
                    $this->primaryKeyLang => $key,
                    'id_lang'             => $k,
                    'name'                => $v['name'],
                    'slug'                => (!isset($v['slug'])) ? '' : $v['slug']
                ];
                // Create the new participant
                $builder->insert($data);
            } else {
                $data = [
                    $this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang},
                    'id_lang'             => $this->tableLang->id_lang,
                    'name'                => $v['name'],
                    'slug'                => (!isset($v['slug'])) ? '' : $v['slug']
                ];
                //print_r($data);
                $builder->set($data);
                $builder->where([$this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang}, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }
}
