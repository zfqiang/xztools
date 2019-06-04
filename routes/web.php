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



Route::get('daka/index','DakaController@index')->name('dakaindex');
Route::match(['get', 'post'],'daka/dakaData','DakaController@dakaData');
Route::match(['get', 'post'],'daka/memberData','DakaController@memberData');
Route::get('daka/exportData','DakaController@exportData');
Route::post('daka/importData','DakaController@importData');

Route::post('daka/importMembers','DakaController@importMembers');
Route::get('daka/exportMembers','DakaController@exportMembers');