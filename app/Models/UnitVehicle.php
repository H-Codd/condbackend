<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitVehicle extends Model
{
    
    use HasFactory;

    protected $hidden = ['id_unit'];

    public $table = 'unitvehicles';

    public $timestamps = false;
}
