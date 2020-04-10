<?php

use Illuminate\Support\Facades\Route;
use App\Http\Resources\Estimator as EstimatorResource;
use App\Data;
use App\Region;
use App\Impact;
use App\SevereImpact;
use App\Http\Controllers\EstimatorController;
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
Route::get('/challenge1','EstimatorController@covid19ImpactEstimator')->name('challenge1');


Route::get('/api/v1/on-covid-19/', function () {
    return new EstimatorResource($this->EstimatorController->covid19ImpactEstimator());
});