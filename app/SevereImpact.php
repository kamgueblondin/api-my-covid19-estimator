<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SevereImpact extends Model
{
    //protected $fillable =['currentlyInfected','infectionsByRequestedTime','severeCasesByRequestedTime','hospitalBedsByRequestedTime','casesForICUByRequestedTime','casesForVentilatorsByRequestedTime','dollarsInFlight'];
    public $currentlyInfected;
    public $infectionsByRequestedTime;
    public $severeCasesByRequestedTime;
    public $hospitalBedsByRequestedTime;
    public $casesForICUByRequestedTime;
    public $casesForVentilatorsByRequestedTime;
    public $dollarsInFlight;
}
