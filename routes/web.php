<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Route::get('test_return0', 'FileController@test0')->name('test_return0');
Route::get('test_return', 'FileController@test')->name('test_return');*/

Route::group([], function () { // This will use the default web middleware

    Auth::routes();

    /*    Route::get('/', function () {
            return view('welcome');
        });*/

    //   Route::get('do_return', ['as' => 'return', 'uses' => 'MetrcPackageController@test']);

    Route::get('/', array('as' => 'start', 'uses' => 'FileController@Start'));
    Route::post('get_order', array('as' => 'get_order', 'uses' => 'FileController@get_order'));
    Route::post('make_manifests', array('as' => 'make_manifests', 'uses' => 'FileController@make_manifests'));

    Route::get('automatic', array('as' => 'automatic', 'uses' => 'AutomaticManifests@index'));

    Route::get('info', array('as' => 'info', 'uses' => 'FileController@info'));
    Route::get('product2metrc', array('as' => 'product2metrc', 'uses' => 'MetrcOdooController@getProducts'));
    Route::post('synchronize', array('as' => 'synchronize', 'uses' => 'MetrcOdooController@synchronize'));

    Route::get('related_product/{metrc_id}/{metrc_product_name}', 'MetrcOdooController@related_product')->name('related_product');
    Route::get('select_product/{ext_id}/{metrc_id}', 'MetrcOdooController@selected_product')->name('select_product');
    Route::get('update_all_items', 'MetrcOdooController@update_all_items')->name('update_all_items');
    Route::get('make_package', 'MetrcPackageController@make_package')->name('make_package');
    Route::get('edit_orderline', 'MetrcPackageController@edit_orderline')->name('edit_orderline');
    Route::get('make_package_return/{id?}/{error_message?}', 'FileController@get_order')->name('make_package_return');
    Route::any('do_return/{id?}/{error_message?}', 'FileController@get_order')->name('do_return');
    Route::post('create_package', 'MetrcPackageController@create_package')->name('create_package');
    Route::post('update_orderline', 'MetrcPackageController@update_orderline')->name('update_orderline');

    Route::get('testpackage', 'MetrcPackageController@create_package')->name('testpackage');

    Route::get('export_so_time_span', 'TimespanController@export_so_time_span')->name('export_so_time_span');
    Route::get('span', 'TimespanController@index')->name('index');
    Route::any('so_time_span', 'TimespanController@so_time_span')->name('so_time_span');

    Route::get('import_tags', 'ImportTagsController@index')->name('import_tags');
    Route::post('do_import', 'ImportTagsController@import_tags')->name('do_import');

    Route::post('import_packets', 'ImportTagsController@import_packets')->name('import_packets');

    // Route::get('make_package/{sales_line}', 'MetrcPackageController@make_package')->name('make_package1');

    Route::any('update_tag', 'MetrcPackageController@update_tag')->name('update_tag');

});
