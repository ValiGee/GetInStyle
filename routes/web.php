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

Route::get('/', [
    'uses' => 'MediaController@index',
    'as' => 'media.index',
]);

Route::get('/media', [
    'uses' => 'MediaController@index',
    'as' => 'media.index',
]);