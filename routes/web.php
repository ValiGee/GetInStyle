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

Route::post('media/preview', [
    'uses' => 'MediaController@preview',
    'as' => 'media.preview'
]);

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('media/search', [
        'as' => 'media.search',
        'uses' => 'MediaController@search',
    ]);

    Route::delete('media/{media}', [
        'uses' => 'MediaController@delete',
        'as' => 'media.delete'
    ]);

    Route::post('media', [
        'uses' => 'MediaController@store',
        'as' => 'media.store'
    ]);

    Route::post('comments/store', [
        'uses' => 'CommentController@store',
        'as' => 'comments.store'
    ]);

    Route::post('media/{media}/like', [
        'as' => 'media.like',
        'uses' => 'MediaController@toggleLike',
    ]);

    Route::post('comments/{comment}/like', [
        'as' => 'comments.like',
        'uses' => 'CommentController@toggleLike',
    ]);

    Route::get('media/user/{userId}', [
        'uses' => 'MediaController@photosByUserId',
        'as' => 'media.photosByUserId'
    ]);
});

Route::get('media/{media}', [
    'uses' => 'MediaController@show',
    'as' => 'media.show'
]);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
