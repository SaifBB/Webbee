<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'date',
        'start_time',
        'end_time',
        'available_slots',
        'booked_slots',
    ];
   
}
