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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{slug?}', 'Index@index');

Route::get('/index/{slug?}', 'Index@index');

Route::get('/assets/{slug?}', 'Assets@index')->where('slug', '(.*)');

Route::get('/articles/{slug?}', 'Articles@index')->where('slug', '(.*)');
