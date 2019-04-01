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
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Auth;
use Route;

Route::get('/', [
    'uses' => 'MediaController@index',
    'as' => '/',
]);

Route::get('media', [
    'uses' => 'MediaController@index',
    'as' => 'media.index'
]);

Route::get('media/create', [
    'uses' => 'MediaController@create',
    'as' => 'media.create'
]);

Route::get('media/{media}', [
    'uses' => 'MediaController@show',
    'as' => 'media.show'
]);

Route::post('media', [
    'uses' => 'MediaController@store',
    'as' => 'media.store'
]);

Route::post('comments/store', [
    'uses' => 'CommentController@store',
    'as' => 'comments.store'
]);


Route::group(['middleware' => ['web', 'auth']], function () {
    $user_id = Auth::id();
    Route::get('media/photosByUserId/{user_id}', ['as' => 'media.photosByUserId', 'uses' => 'MediaController@photosByUserId']);

    Route::delete('media/{media}', [
        'uses' => 'MediaController@delete',
        'as' => 'media.delete'
    ]);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
