<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inventaireEquipement extends Model
{
    protected $table = 'tb_inventaire_equipement';

    public $timestamps = true;

    protected $guarded = ['id'];
}