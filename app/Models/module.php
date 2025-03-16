<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class module extends Model
{
    protected $table = 'tb_modules';

    public $timestamps = true;

    protected $guarded = ['id'];
}

