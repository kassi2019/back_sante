<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class patient extends Model
{
    protected $table = 'tb_patients';

    public $timestamps = true;

    protected $guarded = ['id'];
}
