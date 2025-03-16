<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medicament extends Model
{
    protected $table = 'tb_medicaments';

    public $timestamps = true;

    protected $guarded = ['id'];
}
