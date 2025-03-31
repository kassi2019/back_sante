<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class affectationEquipement extends Model
{
    protected $table = 'tb_affectation_equipements';

    public $timestamps = true;

    protected $guarded = ['id'];
}