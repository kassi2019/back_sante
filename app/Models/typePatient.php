<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class typePatient extends Model
{
    protected $table = 'tb_type_patients';

    public $timestamps = true;

    protected $guarded = ['id'];
}