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

Route::get('/', function (){
    return view('welcome');
});

Route::group(['prefix' => 'api', 'middleware' => 'cors'], function (){
    Route::get('products', 'Api\ProductsController@index');
    Route::get('session', 'Api\PagSeguroController@getSessionId');
    Route::post('order', 'Api\OrdersController@store');
});

