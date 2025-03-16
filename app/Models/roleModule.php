<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class roleModule extends Model
{
    protected $table = 'tb_roles_modules';

    public $timestamps = true;

    protected $guarded = ['id'];
}

