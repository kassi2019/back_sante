<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stockAsc extends Model
{
    protected $table = 'tb_stock_asc';

    public $timestamps = true;

    protected $guarded = ['id'];
}