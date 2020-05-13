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

Route::post('/api/register','UserController@register');
Route::post('/api/login','UserController@login');

Route::resource('/api/curse','CurseController');
Route::resource('/api/section','SectionController');
Route::resource('/api/video','VideoController');
Route::resource('/api/comment','CommentController');
Route::resource('/api/enrollment','EnrollmentController');
Route::resource('/api/recurse','RecurseController');
Route::resource('/api/task','TaskController');


//Ruta de prueba
Route::delete('/api/recurse/delete/{id}','RecurseController@destroy');