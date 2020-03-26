<?php

use Spreadaurora\ci4_menu\Entities\Menu;

if (!function_exists('afficher_menu_admin')) {
    function afficher_menu_admin($parent, $niveau, $array, $table = 'tabs')
    {
        if (!isset($html)) $html = "";
        $niveau_precedent = 0;
        if (!$niveau && !$niveau_precedent) {
            $html .= "\n<ol class=\"dd-list\">\n";
        }
        foreach ($array as $noeud) {

            $menu = new Menu(['id' => $noeud->id]);
            $getNameLang = $menu->getNameLang(Service('Settings')->setting_id_lang);
            // print_r($getNameLang); exit;
            if ($parent == $noeud->id_parent) {
                if ($niveau_precedent < $niveau) {
                    $html .= "\n<ol data-id=\"$noeud->id_parent\" class=\"dd-list\">\n";
                }
                $custom = (!$noeud->id_module) ? ' lien '  : ' page ';
                $slug = (!$noeud->id_module) ? $noeud->slug  : '  ';

                $html .= "<li class=\"dd-item " . $custom . " dd-item-active-$noeud->active\" data-method=\"$noeud->slug\" data-id=\"$noeud->id\" data-niveau=\"$niveau\" data-niveau_precedent=\"$niveau_precedent\" data-parent=\"$parent\">";
                $html .= "<div class=\"dd-handle dd3-handle\"></div>";
                $html .= "<div class=\"dd3-content\"><strong>" . $getNameLang . "</strong> " . $slug;
                $html .= '<div class="dropdown dropdown-inline dd3-action">
                                <button type="button" class="btn btn-hover-danger btn-elevate-hover btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    ' . $custom . ' <i class="fa fa-arrow-alt-circle-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-149px, -189px, 0px);">
                                    <a  data-id="' . $noeud->id . '" class="dropdown-item edit" href="/' . CI_SITE_AREA . '/' . user()->id_company . '/settings-advanced/' . $table . '/edit/' . $noeud->id . '"><i class="kt-nav__link-icon flaticon2-contract"></i>' . lang('Core.edit') . '</a>
                                    <a  data-id="' . $noeud->id . '" class="dropdown-item delete" href="/' . CI_SITE_AREA . '/' . user()->id_company . '/settings-advanced/' . $table . '/delete/' . $noeud->id . '"><i class="kt-nav__link-icon flaticon2-trash"></i>' . lang('Core.delete') . '</a>
                                </div>
                            </div>';
                $html .= "</div>";

                $niveau_precedent = $noeud->depth;
                $html .= afficher_menu_admin($noeud->id, ($noeud->depth + 1), $array);
            }
        }
        if (($niveau_precedent == $niveau) && ($niveau_precedent != 0)) {
            $html .= "</li></ol>\n\n";
        } elseif ($niveau_precedent == $niveau) {
            $html .= "</ol>\n";
        } else {
            $html .= "\n";
        }


        return $html;
    }
}
