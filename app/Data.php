<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{

    //protected $fillable =['region','periodType','timeToElapse','reportedCases','population','totalHospitalBeds'];
    public $region;
    public $periodType;
    public $timeToElapse;
    public $reportedCases;
    public $population;
    public $totalHospitalBeds;
}
