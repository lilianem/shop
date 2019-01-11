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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware('auth')->group(function () {
    Route::get('/index', 'ShopifyController@index');
    Route::get('shop/edit/{id}', 'ShopifyController@edit')->name('shop.edit');
    Route::put('shop/edit/{id}', 'ShopifyController@update')->name('shop.update');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
});
Route::get('/home', 'HomeController@index')->name('home');

/* Route::get('/', function() {
	if(Auth::guest())
	{
		return Redirect::to('login');		
	}
	if(Auth::check())
	{
		return redirect()->route('home');
	}
}); */
