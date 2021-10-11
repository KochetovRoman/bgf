<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    //
    protected $fillable = [
        'gender', 'region', 'detail', 'year', 'ageRange', 'percent', 'value',
    ];
}
