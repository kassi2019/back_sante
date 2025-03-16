<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class zoneUtilisateurs extends Model
{
    protected $table = 'tb_zone_utilisateurs';

    public $timestamps = true;

    protected $guarded = ['id'];
}
