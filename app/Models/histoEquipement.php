<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class histoEquipement extends Model
{
    protected $table = 'tb_histo_equipements';

    public $timestamps = true;

    protected $guarded = ['id'];
}
