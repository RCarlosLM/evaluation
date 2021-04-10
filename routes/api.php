<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {

    Route::group([
      'middleware' => 'auth:api'
    ], function() {

    });
});

// films
Route::post('home/create','HomeController@store');
Route::get('home/show','HomeController@show');

Route::put('/films/{film}',[
    'uses' => 'HomeController@update',
    'as' => 'films.update'
]);
Route::delete('/films/{film}', [
    'uses' => 'HomeController@destroy',
    'as' => 'films.destroy'
]);

// shifts
Route::post('shifts/create','ShiftsController@store');
Route::get('shifts/show/{film}','ShiftsController@show');
Route::put('/shifts/{shift}',[
	'uses' => 'ShiftsController@update',
	'as' => 'shifts.update'
]);
Route::delete('/shifts/{shift}', [
	'uses' => 'ShiftsController@destroy',
	'as' => 'shifts.destroy'
]);
