<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Data;
use App\Region;
use App\Impact;
use App\SevereImpact;

class EstimatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function covid19ImpactEstimator($data)
    {
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
        $impact->severeCasesByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*15/100), 0, '.', '');
        $severeImpact->severeCasesByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*15/100), 0, '.', '');
        
        /*
            hospital beds by requested time
        */
        $impact->hospitalBedsByRequestedTime=(int)number_format(((($data->totalHospitalBeds*35)/100)-$impact->severeCasesByRequestedTime), 0, '.', '');
        $severeImpact->hospitalBedsByRequestedTime=(int)number_format(((($data->totalHospitalBeds*35)/100)-$severeImpact->severeCasesByRequestedTime), 0, '.', '');

        /*
            cases for ICU by requested time
        */
        $impact->casesForICUByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*5/100), 0, '.', '');
        $severeImpact->casesForICUByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*5/100), 0, '.', '');
        /*
            cases for ventilators by requested time
        */
        $impact->casesForVentilatorsByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*2/100), 0, '.', '');
        $severeImpact->casesForVentilatorsByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*2/100), 0, '.', '');

        /*
            dollars in flight
        */
        $impact->dollarsInFlight=(double)number_format(((($impact->infectionsByRequestedTime * $data->region->avgDailyIncomePopulation) * $data->region->avgDailyIncomeInUSD) * ($days)), 2, '.', '');
        $severeImpact->dollarsInFlight=(double)number_format(((($severeImpact->infectionsByRequestedTime * $data->region->avgDailyIncomePopulation) * $data->region->avgDailyIncomeInUSD) * ($days)), 2, '.', '');

      return (['data'=>$data,'impact'=>$impact,'severeImpact'=>$severeImpact]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
         
         return $this->covid19ImpactEstimator($data);
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
         
         return response()->xml($this->covid19ImpactEstimator($data));
    }

}
