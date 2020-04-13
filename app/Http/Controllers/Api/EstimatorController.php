<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Data;
use App\Region;
use App\Impact;
use App\SevereImpact;
use Illuminate\Http\Response;
use App\Log;


class EstimatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	function covid19ImpactEstimator($data)
	{
		$data= json_decode(json_encode((object) $data), FALSE);
		/* 
			best case estimation
		*/
		$impact=new Impact;
		
		/* 
			severe case estimation
		*/
		$severeImpact=new SevereImpact;

		/*
			the number of days
		*/
		$days=0;
		
		/*
			the number of currently infected people
		*/
		$impact->currentlyInfected=$data->reportedCases*10;
		$severeImpact->currentlyInfected=$data->reportedCases*50;
		
		/*
			estimations in days
		*/
		if($data->periodType=="days"){
			
			/*
				the factor
			*/
			$factor=(int)($data->timeToElapse/3);
			$days=$data->timeToElapse;
			
			/*
				infections by requested time
			*/
			$impact->infectionsByRequestedTime=(int)number_format(($impact->currentlyInfected*pow(2, $factor)), 0, '.', '');
			$severeImpact->infectionsByRequestedTime=(int)number_format(($severeImpact->currentlyInfected*pow(2, $factor)), 0, '.', '');
		}
		
		/*
			estimations in weeks
		*/
		if($data->periodType=="weeks"){
			
			/*
				the factor
			*/
			$factor=(int)(($data->timeToElapse*7)/3);
			$days=$data->timeToElapse*7;
			
			/*
				infections by requested time
			*/
			$impact->infectionsByRequestedTime=(int)number_format(($impact->currentlyInfected*pow(2, $factor)), 0, '.', '');
			$severeImpact->infectionsByRequestedTime=(int)number_format(($severeImpact->currentlyInfected*pow(2, $factor)), 0, '.', '');
			
		}
		
		/*
			estimations in months
		*/
		if($data->periodType=="months"){
			
			/*
				the factor
			*/
			$factor=(int)(($data->timeToElapse*30)/3);
			$days=$data->timeToElapse*30;
			
			/*
				infections by requested time
			*/
			$impact->infectionsByRequestedTime=(int)number_format(($impact->currentlyInfected*pow(2, $factor)), 0, '.', '');
			$severeImpact->infectionsByRequestedTime=(int)number_format(($severeImpact->currentlyInfected*pow(2, $factor)), 0, '.', '');
			
		}
		
		/*
			severe cases by requested time
		*/
		$impact->severeCasesByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*0.15), 0, '.', '');
		$severeImpact->severeCasesByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*0.15), 0, '.', '');
		
		/*
			hospital beds by requested time
		*/
		$impact->hospitalBedsByRequestedTime=(int)number_format((ceil((float)($data->totalHospitalBeds*0.35))-$impact->severeCasesByRequestedTime), 0, '.', '');
		$severeImpact->hospitalBedsByRequestedTime=(int)number_format((ceil((float)($data->totalHospitalBeds*0.35))-$severeImpact->severeCasesByRequestedTime), 0, '.', '');
		if($impact->hospitalBedsByRequestedTime>0){
			$impact->hospitalBedsByRequestedTime-=1;
			$severeImpact->hospitalBedsByRequestedTime-=1;
		}
		/*
			cases for ICU by requested time
		*/
		$impact->casesForICUByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*0.05), 0, '.', '');
		$severeImpact->casesForICUByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*0.05), 0, '.', '');
		/*
			cases for ventilators by requested time
		*/
		$impact->casesForVentilatorsByRequestedTime=(int)number_format((int)($impact->infectionsByRequestedTime*0.02), 0, '.', '');
		$severeImpact->casesForVentilatorsByRequestedTime=(int)number_format((int)($severeImpact->infectionsByRequestedTime*0.02), 0, '.', '');

		/*
			dollars in flight
		*/
		$impact->dollarsInFlight=(int)number_format((float)(($impact->infectionsByRequestedTime*$data->region->avgDailyIncomePopulation)*$data->region->avgDailyIncomeInUSD/$days), 2, '.', '');
		$severeImpact->dollarsInFlight=(int)number_format((float)(($severeImpact->infectionsByRequestedTime*$data->region->avgDailyIncomePopulation)*$data->region->avgDailyIncomeInUSD/$days), 2, '.', '');
		
	  return (['data'=>$data,'impact'=>$impact,'severeImpact'=>$severeImpact]);
	}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function default(Request $request)
    {
         $data=new Data;
         $data->region=new Region;
         $data->region->name=$request->region['name'];
         $data->region->avgAge=$request->region['avgAge'];
         $data->region->avgDailyIncomeInUSD=$request->region['avgDailyIncomeInUSD'];
         $data->region->avgDailyIncomePopulation=$request->region['avgDailyIncomePopulation'];
         $data->periodType= $request->periodType;
         $data->timeToElapse= $request->timeToElapse;
         $data->reportedCases= $request->reportedCases;
         $data->population= $request->population;
         $data->totalHospitalBeds=$request->totalHospitalBeds;


         $de=$this->covid19ImpactEstimator($data);
         
        $log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();;

         return $de;
    }

    public function json(Request $request)
    {
         $data=new Data;
         $data->region=new Region;
         $data->region->name=$request->region['name'];
         $data->region->avgAge=$request->region['avgAge'];
         $data->region->avgDailyIncomeInUSD=$request->region['avgDailyIncomeInUSD'];
         $data->region->avgDailyIncomePopulation=$request->region['avgDailyIncomePopulation'];
         $data->periodType= $request->periodType;
         $data->timeToElapse= $request->timeToElapse;
         $data->reportedCases= $request->reportedCases;
         $data->population= $request->population;
         $data->totalHospitalBeds=$request->totalHospitalBeds;


         $de=$this->covid19ImpactEstimator($data);
		 
        $log=new Log;
        $log->method=$request->header();
        $log->path=$request->server();
		$log->status=$request;
		$log->requestime=" diferent";
        $log->save();
		
        $log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19/json";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
         
         return $de;
    }

    public function xml(Request $request)
    {
         $data=new Data;
         $data->region=new Region;
         $data->region->name=$request->region['name'];
         $data->region->avgAge=$request->region['avgAge'];
         $data->region->avgDailyIncomeInUSD=$request->region['avgDailyIncomeInUSD'];
         $data->region->avgDailyIncomePopulation=$request->region['avgDailyIncomePopulation'];
         $data->periodType= $request->periodType;
         $data->timeToElapse= $request->timeToElapse;
         $data->reportedCases= $request->reportedCases;
         $data->population= $request->population;
         $data->totalHospitalBeds=$request->totalHospitalBeds;


         $de=$this->covid19ImpactEstimator($data);
         
        $log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19/xml";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
         
         return response()->xml($de);
    }
    public function logs(Request $request)
    {
		$log=new Log;
        $log->method=$request->header();
        $log->path=$request->server();
		$log->status=$request;
		$log->requestime=" diferent";
        $log->save();
		
		$log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19/logs";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
        $logs=Log::all();
        $text="";
        foreach ($logs as $log) {
            $text.=$log->method."\t\t".$log->path."\t\t".$log->status."\t\t".$log->requestime."ms \n";
        }
		
		header('Content-Type:text/plain');
		$response =new Response($text, 200);
		$response->header('Content-Type', 'text/plain');
		return $response;
                 
    }
	public function logsGet(Request $request)
    {
        $log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19/logs";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
        $logs=Log::all();
        $text="";
        foreach ($logs as $log) {
            $text.=$log->method."\t\t".$log->path."\t\t".$log->status."\t\t".$log->requestime."ms \n";
        }
		
		header('Content-Type:text/plain');
		$response =new Response($text, 200);
		$response->header('Content-Type', 'text/plain');
		return $response;
                 
    }
	public function defaultget(Request $request)
    {
		$log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
                 
    }
	
	public function jsonget(Request $request)
    {
		$log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19/json";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
                 
    }
	
	public function xmlget(Request $request)
    {
		$log=new Log;
        $log->method=$request->server()['REQUEST_METHOD'];
        $log->path="/api/v1/on-covid-19/xml";
		$log->status=http_response_code();
		$log->requestime=str_pad((int)((microtime(true)-$request->server()['REQUEST_TIME_FLOAT'])), 2, "0", STR_PAD_LEFT);
        $log->save();
                 
    }

}
