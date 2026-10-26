<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceMaster extends Model
{
    use HasFactory;
      protected $table = 'place_master';
      public $timestamps = false;
      protected $fillable = [
        'place_name',
        'status',
        'created_by',
        'updated_by'
    ];
}