<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impact extends Model
{
    protected $fillable =['currentlyInfected','infectionsByRequestedTime','severeCasesByRequestedTime','hospitalBedsByRequestedTime','casesForICUByRequestedTime','casesForVentilatorsByRequestedTime','dollarsInFlight'];

}
