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


$routes->group(CI_SITE_AREA, ['namespace' => '\Spreadaurora\ci4_menu\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get('(:num)/(:any)/menus/(:num)', 'AdminMenusController::renderView/$3', ['as' => 'menu-index']);
    $routes->get('(:num)/(:any)/menus/edit/(:num)', 'AdminMenusController::renderForm/$3');
    $routes->post('(:num)/(:any)/menus/edit/(:num)', 'AdminMenusController::postProcess/$3');
    $routes->get('(:num)/(:any)/menus/add', 'AdminMenusController::renderForm');
    $routes->post('(:num)/(:any)/menus/add', 'AdminMenusController::postProcess');
    $routes->get('(:num)/(:any)/menus/delete/(:num)', 'AdminMenusController::delete/$3');
});
