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
     
     return $this->covid19ImpactEstimators($data);
    }

    public function covid19ImpactEstimators($data){
        $impact=new Impact;
        $severeImpact=new SevereImpact;
        $impact->currentlyInfected=$data->reportedCases*10;
        $severeImpact->currentlyInfected=$data->reportedCases*50;
        if($data->periodType=="days"){
            $factor=(int)($data->timeToElapse/3);
            $impact->infectionsByRequestedTime=(int)($impact->currentlyInfected*pow(2, $factor));
            $severeImpact->infectionsByRequestedTime=(int)($severeImpact->currentlyInfected*pow(2, $factor));

            $severeImpact->dollarsInFlight=(float)($severeImpact->infectionsByRequestedTime * 0.65 * 1.5 * $data->timeToElapse);
            $impact->dollarsInFlight=(float)($impact->infectionsByRequestedTime * 0.65 * 1.5 * $data->timeToElapse);
        }

        if($data->periodType=="weeks"){
            $factor=(int)(($data->timeToElapse*7)/3);
            $impact->infectionsByRequestedTime=(int)($impact->currentlyInfected*pow(2, $factor));
            $severeImpact->infectionsByRequestedTime=(int)($severeImpact->currentlyInfected*pow(2, $factor));

            $severeImpact->dollarsInFlight=(float)($severeImpact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*7));
            $impact->dollarsInFlight=(float)($impact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*7));
        }

        if($data->periodType=="months"){
            $factor=(int)(($data->timeToElapse*30)/3);
            $impact->infectionsByRequestedTime=(int)($impact->currentlyInfected*pow(2, $factor));
            $severeImpact->infectionsByRequestedTime=(int)($severeImpact->currentlyInfected*pow(2, $factor));

            $severeImpact->dollarsInFlight=(float)($severeImpact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*30));
            $impact->dollarsInFlight=(float)($impact->infectionsByRequestedTime * 0.65 * 1.5 * ($data->timeToElapse*30));
        }

        $severeImpact->severeCasesByRequestedTime=(int)($severeImpact->currentlyInfected*15/100);
        $impact->severeCasesByRequestedTime=(int)($impact->currentlyInfected*15/100);

        $severeImpact->hospitalBedsByRequestedTime=(int)((($data->totalHospitalBeds*35)/100)-$severeImpact->severeCasesByRequestedTime);
        $impact->hospitalBedsByRequestedTime=(int)((($data->totalHospitalBeds*35)/100)-$impact->severeCasesByRequestedTime);

        $severeImpact->casesForICUByRequestedTime=(int)($severeImpact->infectionsByRequestedTime*5/100);
        $impact->casesForICUByRequestedTime=(int)($impact->infectionsByRequestedTime*5/100);

        $severeImpact->casesForVentilatorsByRequestedTime=(int)($severeImpact->infectionsByRequestedTime*2/100);
        $impact->casesForVentilatorsByRequestedTime=(int)($impact->infectionsByRequestedTime*2/100);


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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
