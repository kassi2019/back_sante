<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class district extends Model
{
    protected $table = 'tb_districts';

    public $timestamps = true;

    protected $guarded = ['id'];
}
