<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class menage extends Model
{
    protected $table = 'tb_menages';

    public $timestamps = true;

    protected $guarded = ['id'];
}
