<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class zoneIntervention extends Model
{
    protected $table = 'tb_zone_interventions';

    public $timestamps = true;

    protected $guarded = ['id'];
}