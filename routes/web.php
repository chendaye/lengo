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
     return 'success';
    $a = new \Lib\Fdfs\Lm();
    dd($a->storage_file_exist('group1', 'aa'));
});

Route::get('fastdfs', '\App\Http\Controllers\TestController@fastdfs');
Route::get('redis', '\App\Http\Controllers\TestController@redis');
