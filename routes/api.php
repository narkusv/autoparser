<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/ParseURL', 'ParseURLController@store')->middleware('auth:api');
Route::get('/ParseURL', 'ParseURLController@list')->middleware('auth:api');
Route::post('/RemoveParseURL', 'ParseURLController@delete')->middleware('auth:api');

Route::get('/Classifieds/{id}', 'ClassifiedController@list')->middleware('auth:api');
Route::get('/New_classifieds', 'ClassifiedController@list_new_classifieds')->middleware('auth:api');
Route::get('/Preview_new_classifieds', 'ClassifiedController@preview_new_classifieds')->middleware('auth:api');
