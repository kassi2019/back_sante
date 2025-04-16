<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stockDistrict extends Model
{
    protected $table = 'tb_stock_districts';

    public $timestamps = true;

    protected $guarded = ['id'];
}
