<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class aireSanitaire extends Model
{
    protected $table = 'tb_aire_sanitaires';

    public $timestamps = true;

    protected $guarded = ['id'];
}