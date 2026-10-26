<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsoleMasterModel extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'console_master';

    // Primary key
    protected $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    // Mass assignable fields
   protected $fillable = [
       'console_name',
        'place_id',
        'limited_person',
        'console_icon',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}