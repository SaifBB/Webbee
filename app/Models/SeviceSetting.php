<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceType;
use App\Models\Days;


class SeviceSetting extends Model
{
    protected $table = 'sevice_settings';

    use HasFactory;
    protected $fillable = [
        'opening_time',
        'closing_time',
        'max_clients_per_slot',
        'slot_duration',
        'cleaning_break_duration',
        'break_start_time',
        'break_end_time',
        // Add other fields here as well
    ];

    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_id');
    }

    public function day()
    {
        return $this->belongsTo(Days::class, 'day_id');
    }
    
    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}
