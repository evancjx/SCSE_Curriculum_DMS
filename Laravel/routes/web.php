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

Auth::routes();

Route::get('/', function(){return redirect('/curriculum');})->name('Home');
Route::get('/curriculum', 'CurriculumController@index')->name('Curriculum List');

Route::get('/search', 'SearchController@index')->name('Search');
Route::post('/search', 'SearchController@search')->name('Search Curriculum');

Route::get('/import', 'ImportExportController@index')->name('Import');
Route::post('/import', 'ImportExportController@import')->name('Import Curriculum');
Route::get('/curriculum/{code}/export', 'ImportExportController@export')->name('Export Curriculum');

Route::get('/curriculum/create', 'CurriculumController@create')->name('Insert Curriculum');
Route::post('/curriculum', 'CurriculumController@store')->name('Store Curriculum');
Route::get('/curriculum/{code}', 'CurriculumController@show')->name('View curriculum');
Route::get('/curriculum/{code}/edit', 'CurriculumController@edit')->name('Edit Curriculum');

Route::get('/display/grad-attr', 'DisplayController@gradattr')->name('Display Grad Attr');
