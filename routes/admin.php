<?php

use Illuminate\Support\Facades\Route;

Route::group(['as' => 'user.', 'prefix' => 'user'], function () {
    Route::get('/', 'UserController@index');
    Route::post('/', 'UserController@create');
    Route::get('/{userId}', 'UserController@show');
    Route::post('/{userId}', 'UserController@update');
    Route::delete('/{userId}', 'UserController@delete');
});

Route::group(['as' => 'site.', 'prefix' => 'site'], function () {
    Route::get('/', 'SiteController@index');
    Route::post('/', 'SiteController@create');
    Route::get('/{siteId}', 'SiteController@show');
    Route::post('/{siteId}', 'SiteController@update');
    Route::delete('/{siteId}', 'SiteController@delete');
    Route::get('/{siteId}/zone', 'SiteController@getZones');
    Route::post('/{siteId}/zone', 'SiteController@addZone');
});

Route::group(['as' => 'zone.', 'prefix' => 'zone'], function () {
    Route::get('/{zoneId}', 'ZoneController@show');
    Route::post('/{zoneId}', 'ZoneController@update');
    Route::delete('/{zoneId}', 'ZoneController@delete');
    Route::get('/{zoneId}/equipment', 'ZoneController@getEquipments');
    Route::post('/{zoneId}/equipment', 'ZoneController@addEquipment');
});

Route::group(['as' => 'equipment.', 'prefix' => 'equipment'], function () {
    Route::get('/{equipmentId}', 'EquipmentController@show');
    Route::post('/{equipmentId}', 'EquipmentController@update');
    Route::delete('/{equipmentId}', 'EquipmentController@delete');
});


Route::group(['as' => 'chef.', 'prefix' => 'chef'], function () {
    Route::get('/{chefId}/planning', 'UserController@indexPlanning');
    Route::post('/{chefId}/planning', 'UserController@createPlanning');
    Route::post('/{chefId}/planning/duplicate', 'UserController@duplicate');
});

Route::group(['as' => 'planning.', 'prefix' => 'planning'], function () {
    Route::get('/{planningId}', 'PlanningController@show');
    Route::post('/{planningId}', 'PlanningController@update');
    Route::delete('/{planningId}', 'PlanningController@delete');
});

Route::group(['as' => 'alert.', 'prefix' => 'alert'], function () {
    Route::get('/chef/{ChefId}', 'AlertController@getAlertsByChef');
    Route::get('/super_chef/{superChefId}', 'AlertController@getAlertsBySuperChef');
});
