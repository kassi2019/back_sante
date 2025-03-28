<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class equipement extends Model
{
    protected $table = 'tb_equipements';

    public $timestamps = true;

    protected $guarded = ['id'];
}