<?php


namespace App\Http\Controllers;

use Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('media', [
    'uses' => 'MediaController@index',
    'as' => 'api.media.index'
]);

Route::get('media/{media}', [
    'uses' => 'MediaController@show',
    'as' => 'api.media.show'
]);

Route::post('media', [
    'uses' => 'MediaController@store',
    'as' => 'api.media.store'
]);

Route::post('register', [
    'uses' => 'Auth\RegisterController@register',
    'as' => 'api.register',
]);

Route::post('register', ['uses' => 'Auth\RegisterController@register']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::delete('media/{media}', [
        'uses' => 'MediaController@delete',
        'as' => 'api.media.delete'
    ]);

    Route::post('comments/store', [
        'uses' => 'CommentController@store',
        'as' => 'api.comments.store'
    ]);

    Route::post('media/{media}/like', [
        'uses' => 'MediaController@toggleLike',
        'as' => 'api.media.like',
    ]);

    Route::post('comments/{comment}/like', [
        'uses' => 'CommentController@toggleLike',
        'as' => 'api.comments.like',
    ]);
});



