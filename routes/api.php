<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->group(function() {
    Route::post('v1/on-covid-19/', 'EstimatorController@default')->name("covid");
	Route::get('v1/on-covid-19/', 'EstimatorController@defaultget')->name("covid");
    Route::post('v1/on-covid-19/json', 'EstimatorController@json')->name("covidjson");
	Route::get('v1/on-covid-19/json', 'EstimatorController@jsonget')->name("covidjson");
    Route::post('v1/on-covid-19/xml', 'EstimatorController@xml')->name("covidxml");
	Route::get('v1/on-covid-19/xml', 'EstimatorController@xmlget')->name("covidxml");
    Route::get('v1/on-covid-19/logs', 'EstimatorController@logsGet')->name("covidlog");
    Route::post('v1/on-covid-19/logs', 'EstimatorController@logs')->name("covidlog");
});