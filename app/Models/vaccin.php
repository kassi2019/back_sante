<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vaccin extends Model
{
    protected $table = 'tb_vaccins';

    public $timestamps = true;

    protected $guarded = ['id'];
}