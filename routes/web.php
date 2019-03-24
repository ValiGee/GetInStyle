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
    'as' => 'media.index',
]);

Route::resource('media', 'MediaController');

Route::group(['middleware' => ['web', 'auth']], function () {
    $user_id = Auth::id();
    Route::get('media/photosByUserId/{user_id}', ['as' => 'media.photosByUserId', 'uses' => 'MediaController@photosByUserId']);
});