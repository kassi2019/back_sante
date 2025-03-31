<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class histoAffectationEquipement extends Model
{
    protected $table = 'tb_histo_affectation_equipements';

    public $timestamps = true;

    protected $guarded = ['id'];
}