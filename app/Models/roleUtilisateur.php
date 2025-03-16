<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class roleUtilisateur extends Model
{
    protected $table = 'tb_roles';

    public $timestamps = true;

    protected $guarded = ['id'];
}

