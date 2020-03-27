<?php

namespace Spreadaurora\ci4_menu\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use CodeIgniter\HTTP\RedirectResponse;

use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use App\Entities\Module;
use App\Models\ModulesModel;
use Spreadaurora\ci4_menu\Entities\Menu;
use Spreadaurora\ci4_menu\Entities\MenuItem;
use Spreadaurora\ci4_menu\Models\MenusModel;
use Spreadaurora\ci4_menu\Models\MenusItemsModel;

class AdminMenusController extends AdminController
{
    /**
     *  * @var Module */
    public $module = true;
    public $controller = 'menus';
    public $item = 'menu';
    public $type = 'Spreadaurora/ci4_menu';
    public $pathcontroller  = '/public/menus';
    public $fieldList = 'name';
    public $add = true;
    public $multilangue = true;
    public $multilangue_list = true;

    protected $instances = [];


    public function __construct()
    {
        parent::__construct();
        helper('Menu');
        $this->controller_type = 'adminmenus';
        $this->module = "menus";
        $this->tableModel  = new MenusModel();
        $this->moduleModel  = new ModulesModel();
        $this->menuItemModel  = new MenusItemsModel();
    }

    public function renderView($id = null)
    {
        $parent =  parent::renderView();
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }
        $this->data['multilangue_list'] = $this->multilangue_list;
        $id_menu_item = (int) $id;
        $this->data['menu_item'] = $this->tableModel->getMenusItem($id_menu_item);
        if (empty($this->data['menu_item'])) {
            Tools::set_message('danger', lang('Core.item_no_exist'), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . '/' . user()->id_company . $this->pathcontroller . '/1');
        }


        $this->setTag('title', lang('Core.menu'));
        $this->data['menu'] = $this->tableModel->where('id_menu_item', $id_menu_item)->orderBy('left', 'ACS')->get()->getResult();

        //Get list module
        $modules = Service('Modules');
        $list_modules = $modules->getAll();
        if (!empty($list_modules)) {
            foreach ($list_modules as $module) {
                if ($module->namespace != 'Spreadaurora\ci4_menu') {
                    $className = '\\' . $module->namespace . '\Models\\' . ucfirst($module->name) . 'Model';
                    if (class_exists($className)) {
                        $this->instances[$module->namespace] = new $className();
                        $this->instances[$module->namespace] = (object) array('id_module' => $module->id_module, 'items' => $this->instances[$module->namespace]->getListByMenu());
                    }
                }
            }
        }
        $this->data['modules'] = $this->instances;
        $this->data['menu_items'] = $this->tableModel->getMenusItems();


        return view($this->get_current_theme_view('index', 'Spreadaurora/ci4_menu'), $this->data);
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
                    return $this->respond(['status' => false, 'message' => lang('Core.une errur est survenue') .  ' : ' . print_r($error, true)], 200);
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
            $menu->id_menu_item = $output['id_menu_item'];
            $menu->active       = 1;
            $menu->position     = 1;
            $menu->depth        = $output['depth'];
            $menu->left         = $output['left'];
            $menu->right        = $output['right'];
            $menu->id_parent    = (isset($output['parent_id'])) ? $output['parent_id'] : 0;
            $menu->slug = "/" . strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $output['slug'])));
            $menu->icon = null;
            if ($this->tableModel->save($menu) != false) {
                if (isset($output['edit_form'])) {
                    $menuId = $output['id'];
                } else {
                    $menuId = $this->tableModel->insertID();
                }

                $this->tabs_langs = $output['lang'];
                $menuItem = new Menu();
                $menuItem->saveLang($this->tabs_langs, $menuId);
                $this->data['menu'] = $this->tableModel->where('id_menu_item', $menu->id_menu_item)->orderBy('left', 'ACS')->get()->getResult();
                $this->data['menu_item'] = $this->tableModel->getMenusItems();
                $html = view($this->get_current_theme_view('__form_section/get_menu', 'Spreadaurora/ci4_menu'), $this->data);
                return $this->respond(['status' => true, 'type' => lang('Core.cool_success'), 'message' => lang('Core.saved_data'), 'html' => $html], 200);
            } else {
                return $this->respond(['status' => false, 'message' => lang('Core.une errur est survenue') .  ' : ' . print_r($menu, true)], 200);
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
                        //$page =  Spreadaurora\ci4_page\Entities\Page();
                        $menu                 = new Menu();
                        $menu->id_menu_item   = $output['id_menu_item'];
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
                            $this->tabs_langs = $lang;
                            $menuItem = new Menu();
                            $menuItem->saveLang($this->tabs_langs, $menuId);
                            $this->data['menu'] = $this->tableModel->where('id_menu_item', $menu->id_menu_item)->orderBy('left', 'ACS')->get()->getResult();
                            $this->data['menu_item'] = $this->tableModel->getMenusItems();
                            $html = view($this->get_current_theme_view('__form_section/get_menu', 'Spreadaurora/ci4_menu'), $this->data);
                            return $this->respond(['status' => true, 'type' => lang('Core.cool_success'), 'message' => lang('Core.saved_data'), 'html' => $html], 200);
                        } else {
                            return $this->respond(['status' => false, 'message' => lang('Core.une errur est survenue') .  ' : ' . print_r($menu, true)], 200);
                        }
                    }
                }
            }
        }
    }

    public function ajaxProcessGetMenu()
    {
        if ($value = $this->request->getPost('value')) {
            $this->data['form'] = $this->tableModel->find($value);
            $this->data['menu_items'] = $this->tableModel->getMenusItems();
            $html = view($this->get_current_theme_view('__form_section/edit_menu', 'Spreadaurora/ci4_menu'), $this->data);
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
                        $this->tableModel->set('id_parent', '0')->where('id_parent', $l->id)->update();
                    }
                }

                $this->data['menu'] = $this->tableModel->where('id_menu_item', $menu->id_menu_item)->orderBy('left', 'ACS')->get()->getResult();
                $this->data['menu_item'] = $this->tableModel->getMenusItems();
                $html = view($this->get_current_theme_view('__form_section/get_menu', 'Spreadaurora/ci4_menu'), $this->data);
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
            return redirect()->to('/' . CI_SITE_AREA . '/' . user()->id_company . $this->pathcontroller . '/1');
        }

        $this->data['backPathController'] = $this->pathcontroller . '/1';
        $this->data['multilangue'] = (isset($this->multilangue)) ? $this->multilangue : false;;

        if (is_null($id)) {
            $this->data['action'] = 'add';
            $this->data['add_title']  = lang('Core.add_' . $this->item);
            $this->data['form'] = new MenuItem($this->request->getPost());
        } else {
            $this->data['action'] = 'edit';
            $this->data['edit_title'] = lang('Core.edit_' . $this->item);
            $this->data['form'] = $this->tableModel->getMenusItem($id);
            $this->data['title_detail'] = $this->data['form']->name;
        }
        return view($this->get_current_theme_view('form', 'Spreadaurora/ci4_menu'), $this->data);
    }

    public function postProcessEdit($param)
    {
        // validate
        $menu = new MenusItemsModel();
        $rules = [
            'name' => 'required',
        ];
        if (!$this->validate($rules)) {
            Tools::set_message('danger', $this->validator->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $menuBase = new MenuItem($this->request->getPost());
        $menuBase->handle = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $menuBase->name)));

        if (!$menu->save($menuBase)) {
            Tools::set_message('danger', $menu->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/menus',
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $menuBase->id_menu_item,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function postProcessAdd($param)
    {
        // validate
        $menu = new MenusItemsModel();
        $rules = [
            'name' => 'required',
        ];
        if (!$this->validate($rules)) {
            Tools::set_message('danger', $this->validator->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $menuBase = new MenuItem($this->request->getPost());
        $menuBase->handle = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $menuBase->name)));

        if (!$menu->save($menuBase)) {
            Tools::set_message('danger', $menu->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $id_menu_item = $menu->insertID();

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/menus',
            'action'                => 'add',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $id_menu_item,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    // Delete
    public function delete($id_menu_item): RedirectResponse
    {
        // (Soft) delete
        $this->menuItemModel->delete($id_menu_item);
        $this->tableModel->deleteItem($id_menu_item);

        Tools::set_message('success', lang('Core.delete_data'), lang('Core.cool_success'));
        return redirect()->to('/' . CI_SITE_AREA . '/' . user()->id_company . $this->pathcontroller . '/1');
    }
}
