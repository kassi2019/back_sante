<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class typeEquipement extends Model
{
    protected $table = 'tb_type_equipements';

    public $timestamps = true;

    protected $guarded = ['id'];
}
