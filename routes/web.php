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

Route::get('login', array('as' => 'login', 'uses' => 'metrcController@index'));
Route::get('mpost', array('as' => 'mpost', 'uses' => 'HttpController@metrc_posts'));
Route::get('mget', array('as' => 'mget', 'uses' => 'HttpController@metrc_gets'));

Route::get('go-home', array('as' => 'go-home', 'uses' => 'MetrcMainController@index'));
Route::get('go-back', array('as' => 'go-back', 'uses' => 'MetrcTestController@index'));

Route::resource('metrcmains', 'MetrcMainController');
Route::resource('metrctests', 'MetrcTestController');

Route::get('go-detail/{id}', array('as' => 'go-detail', 'uses' => 'MetrcTestController@index'));
