<?php

namespace Adnduweb\Ci4_menu\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use CodeIgniter\HTTP\RedirectResponse;

use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use App\Entities\Module;
use App\Models\ModuleModel;
use Adnduweb\Ci4_menu\Entities\Menu;
use Adnduweb\Ci4_menu\Entities\MenuMain;
use Adnduweb\Ci4_menu\Models\MenuModel;
use Adnduweb\Ci4_menu\Models\MenuMainModel;

class AdminMenuController extends AdminController
{

    /**
     *  Module Object
     */
    public $module = true;

    /**
     * name controller
     */
    public $controller = 'menu';

    /**
     * Localize slug
     */
    public $pathcontroller  = '/menus';

    /**
     * Localize namespace
     */
    public $namespace = 'Adnduweb/Ci4_menu';

    /**
     * Id Module
     */
    protected $idModule;

    /**
     * Localize slug
     */
    public $dirList  = 'menus';

    /**
     * Display default list column
     */
    public $fieldList = 'name';

    /**
     * Bouton add
     */
    public $add = true;

    /**
     * Display Multilangue
     */
    public $multilangue = true;

    /**
     * Display Multilangue Nesteed
     */
    public $multilangue_list = true;

    /**
     * Event fake data
     */
    public $fake = false;

    /**
     * Update item List
     */
    public $toolbarUpdate = true;

    /**
     * Retour
     */
    public $toolbarBack = true;

    /**
     * @var \App\Models\FormModel
     */
    public $tableModel;

    /**
     * Instance Object
     */
    protected $instances = [];


    /**
     * Page constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        helper('Menu');
        $this->controller_type = 'adminmenus';
        $this->tableModel  = new MenuModel();
        $this->moduleModel  = new ModuleModel();
        $this->menuMainModel  = new MenuMainModel();
    }

    public function renderView($id = null)
    {
        $parent =  parent::renderView();
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }
        $this->data['multilangue_list']  = $this->multilangue_list;
        $this->data['fakedata']          = $this->fake;
        $this->data['changeCategorie']   = $this->changeCategorie;
        $this->data['addPathController'] = '/' . config('Menu')->urlMenuAdmin .  $this->pathcontroller . '/add';
                    $menu_main_id        = (int) $id;
        $this->data['menu_item']         = $this->tableModel->getMenuMain($menu_main_id);
        if (empty($this->data['menu_item'])) {
            Tools::set_message('danger', lang('Core.item_no_exist'), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller . '/1');
        }


        $this->setTag('title', lang('Core.menu'));
        $this->data['menu'] = $this->tableModel->getMenuAdmin($menu_main_id);
        // print_r($this->data['menu']);
        // exit;

        //Get list module
        $modules = Service('Modules');
        $list_modules = $modules->getAll();
        if (!empty($list_modules)) {
            // print_r($list_modules);
            // exit;
            foreach ($list_modules as $module) {
                if ($module->namespace != 'Adnduweb\Ci4_menu' && $module->namespace != 'Adnduweb\Ci4_customer') {
                    $className = '\\' . $module->namespace . '\Models\\' . ucfirst($module->name) . 'Model';
                    if (class_exists($className)) { 
                        $this->instances[$module->namespace] = new $className();
                        $this->instances[$module->namespace] = (object) array('id_module' => $module->id_module, 'items' => $this->instances[$module->namespace]->getListByMenu());
                    }
                }
            }
        }
        $this->data['modules'] = $this->instances;
        $this->data['menu_items'] = $this->tableModel->getMenusMains();

        return view($this->get_current_theme_view('index', $this->namespace), $this->data);
    }

    public function ajaxProcessSortMenu()
    {
        //print_r($this->request->getPost('value')); exit;
        if ($value = $this->request->getPost('value')) {
            $error = [];
            if (is_array($value)) {
                foreach ($value as $v) {
                    $menu = [];
                    if ($v['id'] == '-1') {
                        unset($v['id']);
                        $menu = new Menu();
                    } else {
                        $menu = $this->tableModel->find($v['id']);
                    }
                    $menu->active = 1;
                    $menu->position = 1;
                    $menu->depth = $v['depth'];
                    $menu->left = $v['left'];
                    $menu->right = $v['right'];
                    $menu->id_parent = (isset($v['parent_id'])) ? $v['parent_id'] : 0;
                    $menu->slug =  '/';
                    if ($this->tableModel->save($menu) == false) {
                        $error[] = $menu;
                    }
                }
                if (count($error) > 0) {
                    return $this->respond(['status' => false, 'message' => lang('Core.une erreur est survenue') .  ' : ' . print_r($error, true)], 200);
                } else {
                    return $this->respond(['status' => true, 'type' => lang('Core.cool_success'), 'message' => lang('Core.saved_data')], 200);
                }
                exit;
            }
        }
    }

    public function ajaxProcessSaveMenuCustom()
    {
        if ($value = $this->request->getPost('value')) {
            parse_str($value, $output);
            //print_r($output);
            // exit;
            if (isset($output['edit_form'])) {
                $menu = $this->tableModel->find($output['id']);
            } else {
                $menu               = new Menu();
            }
            $menu->menu_main_id = $output['menu_main_id'];
            $menu->active       = 1;
            $menu->position     = 1;
            // $menu->depth        = $output['depth'];
            // $menu->left         = $output['left'];
            // $menu->right        = $output['right'];
            // $menu->id_parent    = (isset($output['parent_id'])) ? $output['parent_id'] : 0;
            //$menu->slug = ($output['slug'] == "#") ? "#" : strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $output['slug'])));
            //$menu->slug = $output['slug'];
            $menu->icon = null;

            if ($this->tableModel->save($menu) != false) {
                if (isset($output['edit_form'])) {
                    $menuId = $output['id'];
                } else {
                    $menuId = $this->tableModel->insertID();
                }

                $this->tabs_langss = $output['lang'];
                $menuMain = new Menu();
                $menuMain->saveLang($this->tabs_langss, $menuId);
                $this->data['menu'] = $this->tableModel->getMenuAdmin($menu->menu_main_id);
                $this->data['menu_item'] = $this->tableModel->getMenusMains();
                $html = view($this->get_current_theme_view('__form_section/get_menu', $this->namespace), $this->data);
                return $this->respond(['status' => true, 'type' => lang('Core.cool_success'), 'message' => lang('Core.saved_data'), 'html' => $html], 200);
            } else {
                return $this->respond(['status' => false, 'message' => lang('Core.une erreur est survenue') .  ' : ' . print_r($menu, true)], 200);
            }
        }
    }

    public function ajaxProcessSaveMenu()
    {
        if ($value = $this->request->getPost('value')) {
            parse_str($value, $output);
            // print_r($output);
            // exit;
            if (isset($output['page-menu'])) {
                foreach ($output['page-menu'] as $k => $v) {
                    foreach ($v as $id_item_module => $lang) {
                        //$page =  Adnduweb\Ci4_page\Entities\Page();
                        $menu                 = new Menu();
                        $menu->menu_main_id   = $output['menu_main_id'];
                        $menu->active         = 1;
                        $menu->position       = 1;
                        $menu->depth          = $output['depth'];
                        $menu->left           = $output['left'];
                        $menu->right          = $output['right'];
                        $menu->id_parent      = (isset($output['parent_id'])) ? $output['parent_id'] : 0;
                        $menu->slug           = '/';
                        $menu->id_module      = $k;
                        $menu->id_item_module = $id_item_module;
                        if ($this->tableModel->save($menu) != false) {
                            $menuId = $this->tableModel->insertID();
                            $base64_decode = base64_decode($lang);
                            $lang = unserialize($base64_decode);
                            $this->tabs_langss = $lang;
                            $menuMain = new Menu();
                            $menuMain->saveLang($this->tabs_langss, $menuId);
                        } else {
                            return $this->respond(['status' => false, 'message' => lang('Core.une errur est survenue') .  ' : ' . print_r($menu, true)], 200);
                        }
                    }
                }

                $this->data['menu'] = $this->tableModel->getMenuAdmin($output['menu_main_id']);
                $this->data['menu_item'] = $this->tableModel->getMenusMains();
                $html = view($this->get_current_theme_view('__form_section/get_menu', $this->namespace), $this->data);
                return $this->respond(['status' => true, 'type' => lang('Core.cool_success'), 'message' => lang('Core.saved_data'), 'html' => $html], 200);
            }
        }
    }

    public function ajaxProcessGetMenu()
    {
        if ($value = $this->request->getPost('value')) {
            $this->data['form'] = $this->tableModel->find($value);
            $this->data['menu_items'] = $this->tableModel->getMenusMains();
            $html = view($this->get_current_theme_view('__form_section/edit_menu', $this->namespace), $this->data);
            return $this->respond(['status' => true, 'html' => $html], 200);
        }
    }

    public function ajaxProcessDeleteMenuItem()
    {
        if ($value = $this->request->getPost('value')) {
            // On recherche la ligne
            $menu = $this->tableModel->find($value);
            if ($this->tableModel->delete($value) == true) {
                $this->tableModel->set('id_parent', '0')->where('id_parent', $value)->update();

                // On recherche les autres lignes si sous menus
                $list = $this->tableModel->where('id_parent', $menu->id_parent)->get()->getResult();
                if (!empty($list)) {
                    foreach ($list as $l) {
                        $this->tableModel->set('id_parent', '0')->where('id_parent', $l->id_menu)->update();
                    }
                }

                $this->data['menu'] = $this->tableModel->getMenuAdmin($menu->menu_main_id);
                $this->data['menu_item'] = $this->tableModel->getMenusMains();
                $html = view($this->get_current_theme_view('__form_section/get_menu', $this->namespace), $this->data);
                return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_selected_records_have_been_deleted'), 'html' => $html], 200);
            } else {
                return $this->respond(['status' => false, 'database' => true, 'display' => 'modal', 'message' => lang('Js.aucun_enregistrement_effectue')], 200);
            }
        }
    }

    public function renderForm($id = null)
    {

        if (!has_permission(ucfirst($this->controller) . '::edit', user()->id)) {
            Tools::set_message('danger', lang('Core.not_acces_permission'), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller . '/1');
        }

        $this->data['toolbarBack']        = $this->toolbarBack;
        $this->data['backPathController'] =  '/' . config('Menu')->urlMenuAdmin .  $this->pathcontroller . '/1';
        $this->data['multilangue']        = (isset($this->multilangue)) ? $this->multilangue : false;;

        if (is_null($id)) {
            $this->data['action'] = 'add';
            $this->data['add_title']  = lang('Core.add_' . $this->controller);
            $this->data['form'] = new MenuMain($this->request->getPost());
        } else {
            $this->data['action'] = 'edit';
            $this->data['edit_title'] = lang('Core.edit_' . $this->controller);
            $this->data['form'] = $this->tableModel->getMenuMain($id);
            $this->data['title_detail'] = $this->data['form']->name;
        }
        return view($this->get_current_theme_view('form', $this->namespace), $this->data);
    }

    public function postProcessEdit($param)
    {
        // validate
        $menu = new MenuMainModel();
        $rules = [
            'name' => 'required',
        ];
        if (!$this->validate($rules)) {
            Tools::set_message('danger', $this->validator->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $menuBase = new MenuMain($this->request->getPost());
        $menuBase->handle = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $menuBase->name)));

        if (!$menu->save($menuBase)) {
            Tools::set_message('danger', $menu->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/public/menus',
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $menuBase->id,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function postProcessAdd($param)
    {
        // validate
        $menu = new MenuMainModel();
        $rules = [
            'name' => 'required',
        ];
        if (!$this->validate($rules)) {
            Tools::set_message('danger', $this->validator->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $menuBase = new MenuMain($this->request->getPost());
        $menuBase->handle = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $menuBase->name)));

        if (!$menu->save($menuBase)) {
            Tools::set_message('danger', $menu->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $menu_main_id = $menu->insertID();

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/public/menus',
            'action'                => 'add',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $menu_main_id,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    // Delete
    public function delete($menu_main_id): RedirectResponse
    {
        // (Soft) delete
        $this->menuMainModel->delete($menu_main_id);
        $this->tableModel->deleteItem($menu_main_id);

        Tools::set_message('success', lang('Core.delete_data'), lang('Core.cool_success'));
        return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller . '/1');
    }
}
