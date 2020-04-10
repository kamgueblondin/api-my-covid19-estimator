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
	 $data=new Data;
	 $data->region=new Region;
	 $data->region->name="Africa";
     $data->region->avgAge=19.7;
     $data->region->avgDailyIncomeInUSD=5;
     $data->region->avgDailyIncomePopulation=0.71;
	 $data->periodType= "days";
     $data->timeToElapse= 58;
     $data->reportedCases= 674;
     $data->population= 66622705;
     $data->totalHospitalBeds=1380614;
    return new EstimatorResource($data);
});