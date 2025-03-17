<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class patientVaccin extends Model
{
    protected $table = 'tb_patient_vaccins';

    public $timestamps = true;

    protected $guarded = ['id'];
}
