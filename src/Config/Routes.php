<?php

// $routes->group('album', ['namespace' => 'Album\Controllers'], function ($routes) {
// 	// URI: /album
// 	$routes->get('', 'Album::index', ['as' => 'album-index']);

// 	// URI: /album/add
// 	$routes->match(['get', 'post'], 'add', 'Album::add', ['as' => 'album-add']);

// 	// example URI: /album/delete/1
// 	$routes->get('delete/(:num)', 'Album::delete/$1', ['as' => 'album-delete']);

// 	// example URI: /album/1
// 	$routes->match(['get', 'post'], 'edit/(:num)', 'Album::edit/$1', ['as' => 'album-edit']);
// });

// On définit la langue dans la route


$routes->group(CI_SITE_AREA, ['namespace' => '\Adnduweb\Ci4_menu\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get(config('Menu')->urlMenuAdmin . '/menus/(:num)', 'AdminMenuController::renderView/$1', ['as' => 'menu-index']);
    $routes->get(config('Menu')->urlMenuAdmin . '/menus/edit/(:num)', 'AdminMenuController::renderForm/$1');
    $routes->post(config('Menu')->urlMenuAdmin . '/menus/edit/(:num)', 'AdminMenuController::postProcess/$1');
    $routes->get(config('Menu')->urlMenuAdmin . '/menus/add', 'AdminMenuController::renderForm');
    $routes->post(config('Menu')->urlMenuAdmin . '/menus/add', 'AdminMenuController::postProcess');
    $routes->get(config('Menu')->urlMenuAdmin . '/menus/delete/(:num)', 'AdminMenuController::delete/$1');
});
