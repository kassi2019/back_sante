<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stockSuperviseur extends Model
{
    protected $table = 'tb_stock_superviseurs';

    public $timestamps = true;

    protected $guarded = ['id'];
}