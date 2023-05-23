<?php

use Illuminate\Support\Facades\Route;


/************** superChef **************/

Route::group(['as' => 'chef.', 'prefix' => 'chef', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/', 'ChefController@index');
    Route::post('/', 'ChefController@create');
    Route::get('/{chefId}', 'ChefController@show');
    Route::post('/{chefId}', 'ChefController@update');
    Route::delete('/{chefId}', 'ChefController@delete');
});

Route::group(['as' => 'site.', 'prefix' => 'site', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/', 'SiteController@index');
    Route::post('/', 'SiteController@create');
    Route::get('/{siteId}', 'SiteController@show');
    Route::post('/{siteId}', 'SiteController@update');
    Route::delete('/{siteId}', 'SiteController@delete');
    Route::get('/{siteId}/zone', 'SiteController@getZones');
    Route::post('/{siteId}/zone', 'SiteController@addZone');
});

Route::group(['as' => 'zone.', 'prefix' => 'zone', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/{zoneId}', 'ZoneController@show');
    Route::post('/{zoneId}', 'ZoneController@update');
    Route::delete('/{zoneId}', 'ZoneController@delete');
    Route::get('/{zoneId}/equipment', 'ZoneController@getEquipments');
    Route::post('/{zoneId}/equipment', 'ZoneController@addEquipment');
});

Route::group(['as' => 'equipment.', 'prefix' => 'equipment', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/{equipmentId}', 'EquipmentController@show');
    Route::post('/{equipmentId}', 'EquipmentController@update');
    Route::delete('/{equipmentId}', 'EquipmentController@delete');
});

Route::group(['as' => 'chef.', 'prefix' => 'chef', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/{chefId}/planning', 'UserController@indexPlanning');
    Route::post('/{chefId}/planning', 'UserController@createPlanning');
    Route::post('/{chefId}/planning/duplicate', 'UserController@duplicate');
});

Route::group(['as' => 'planning.', 'prefix' => 'planning', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/{planningId}', 'PlanningController@show');
    Route::post('/{planningId}', 'PlanningController@update');
    Route::delete('/{planningId}', 'PlanningController@delete');
});

Route::group(['as' => 'super_chef_alert.', 'prefix' => 'super_chef_alert', 'middleware' => ['userRole:superChef']], function () {
    Route::get('/', 'AlertController@getChefsAlerts');
    Route::get('/{chefId}', 'AlertController@getAlertsByChef');
});


/************** Chef **************/

Route::group(['as' => 'chef_guard.', 'prefix' => 'chef_guard', 'middleware' => ['userRole:chef']], function () {
    Route::get('/', 'ChefGuardController@index');
    Route::post('/', 'ChefGuardController@create');
    Route::get('/{guardId}', 'ChefGuardController@show');
    Route::post('/{guardId}', 'ChefGuardController@update');
    Route::delete('/{guardId}', 'ChefGuardController@delete');
    Route::get('/{guardId}/equipment_history', 'ChefGuardController@getEquipmentHistory');
});

Route::group(['as' => 'chef_site.', 'prefix' => 'chef_site', 'middleware' => ['userRole:chef']], function () {
    Route::get('/', 'ChefSiteController@show');
    Route::get('/zone', 'ChefSiteController@getZones');
});

Route::group(['as' => 'chef_zone.', 'prefix' => 'chef_zone', 'middleware' => ['userRole:chef']], function () {
    Route::get('/{zoneId}', 'ChefZoneController@show');
    Route::get('/{zoneId}/equipment', 'ChefZoneController@getEquipments');
});

Route::group(['as' => 'chef_planning.', 'prefix' => 'chef_planning', 'middleware' => ['userRole:chef']], function () {
    Route::get('/', 'ChefPlanningController@index');
    Route::post('/', 'ChefPlanningController@create');
    Route::get('/{planningId}', 'ChefPlanningController@show');
    Route::post('/duplicate', 'ChefPlanningController@duplicate');
    Route::post('/{planningId}', 'ChefPlanningController@update');
    Route::delete('/{planningId}', 'ChefPlanningController@delete');
});

Route::group(['as' => 'chef_alert.', 'prefix' => 'chef_alert', 'middleware' => ['userRole:chef']], function () {
    Route::get('/', 'AlertController@getGuardsAlerts');
    Route::post('/{alertId}/comment', 'AlertController@addComment');
});

Route::group(['as' => 'chef_comment.', 'prefix' => 'chef_comment', 'middleware' => ['userRole:chef']], function () {
    Route::get('/', 'CommentController@index');
    Route::get('/{commentId}', 'CommentController@show');
    Route::post('/{commentId}', 'CommentController@update');
    Route::delete('/{commentId}', 'CommentController@delete');
});


/************** superChef,admin **************/

Route::group(['as' => 'guard.', 'prefix' => 'guard', 'middleware' => ['userRole:superChef,admin']], function () {
    Route::get('/', 'GuardController@index');
    Route::post('/', 'GuardController@create');
    Route::get('/{guardId}', 'GuardController@show');
    Route::post('/{guardId}', 'GuardController@update');
    Route::delete('/{guardId}', 'GuardController@delete');
    Route::get('/{guardId}/equipment_history', 'GuardController@getEquipmentHistory');
});


/************** superChef,admin,chef **************/

Route::group(['as' => 'profile.', 'prefix' => 'profile', 'middleware' => ['userRole:superChef,admin,chef']], function () {
    Route::get('/', 'UserController@profile');
    Route::post('/', 'UserController@update');
    Route::post('/password', 'UserController@password');
});
