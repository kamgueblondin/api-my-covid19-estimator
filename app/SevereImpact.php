<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SevereImpact extends Model
{
    protected $fillable =['currentlyInfected','infectionsByRequestedTime','severeCasesByRequestedTime','hospitalBedsByRequestedTime','casesForICUByRequestedTime','casesForVentilatorsByRequestedTime','dollarsInFlight'];
}
