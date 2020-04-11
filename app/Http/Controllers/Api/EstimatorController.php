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
    public function index()
    {
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
     
     return $this->covid19ImpactEstimator($data);
    }

    public function covid19ImpactEstimator($data)
    {
        /**/
        $impact=new Impact;
        $severeImpact=new SevereImpact;
        
        /**/
        $impact->currentlyInfected=$data->reportedCases*10;
        $severeImpact->currentlyInfected=$data->reportedCases*50;
        
        /**/
        if($data->periodType=="days"){
            
            /**/
            $factor=(int)($data->timeToElapse/3);
            
            /**/
            $impact->infectionsByRequestedTime=(int)number_format(($impact->currentlyInfected*pow(2, $factor)), 0, '.', '');
            $severeImpact->infectionsByRequestedTime=(int)number_format(($severeImpact->currentlyInfected*pow(2, $factor)), 0, '.', '');
            
            /**/
            $severeImpact->dollarsInFlight=number_format(($severeImpact->infectionsByRequestedTime * 0.65 * 1.5 * $data->timeToElapse), 2, '.', '');
            $impact->dollarsInFlight=number_format(($impact->infectionsByRequestedTime * 0.65 * 1.5 * $data->timeToElapse), 2, '.', '');
        }
        
        /**/
        if($data->periodType=="weeks"){
            
            /**/
            $factor=(int)(($data->timeToElapse*7)/3);
            
            /**/
            $impact->infectionsByRequestedTime=(int)number_format(($impact->currentlyInfected*pow(2, $factor)), 0, '.', '');
            $severeImpact->infectionsByRequestedTime=(int)number_format(($severeImpact->currentlyInfected*pow(2, $factor)), 0, '.', '');
            
            /**/
            $severeImpact->dollarsInFlight=number_format(($severeImpact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*7)), 2, '.', '');
            $impact->dollarsInFlight=number_format(($impact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*7)), 2, '.', '');
        }
        
        /**/
        if($data->periodType=="months"){
            
            /**/
            $factor=(int)(($data->timeToElapse*30)/3);
            
            /**/
            $impact->infectionsByRequestedTime=(int)number_format(($impact->currentlyInfected*pow(2, $factor)), 0, '.', '');
            $severeImpact->infectionsByRequestedTime=(int)number_format(($severeImpact->currentlyInfected*pow(2, $factor)), 0, '.', '');
            
            /**/
            $severeImpact->dollarsInFlight=number_format(($severeImpact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*30)), 2, '.', '');
            $impact->dollarsInFlight=number_format(($impact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*30)), 2, '.', '');
        }
        
        /**/
        $severeImpact->severeCasesByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*15/100), 0, '.', '');
        $impact->severeCasesByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*15/100), 0, '.', '');
        
        /**/
        $severeImpact->hospitalBedsByRequestedTime=(int)number_format(((($data->totalHospitalBeds*35)/100)-$severeImpact->severeCasesByRequestedTime), 0, '.', '');
        $impact->hospitalBedsByRequestedTime=(int)number_format(((($data->totalHospitalBeds*35)/100)-$impact->severeCasesByRequestedTime), 0, '.', '');

        /**/
        $severeImpact->casesForICUByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*5/100), 0, '.', '');
        $impact->casesForICUByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*5/100), 0, '.', '');

        /**/
        $severeImpact->casesForVentilatorsByRequestedTime=(int)number_format(($severeImpact->infectionsByRequestedTime*2/100), 0, '.', '');
        $impact->casesForVentilatorsByRequestedTime=(int)number_format(($impact->infectionsByRequestedTime*2/100), 0, '.', '');


      return (['data'=>$data,'impact'=>$impact,'severeImpact'=>$severeImpact]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return($request->all());
    }

}
