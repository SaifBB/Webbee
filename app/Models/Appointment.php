<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id',
        'email',
        'first_name',
        'last_name'     
    ];

    public function slot()
{
    return $this->belongsTo(Slot::class);
}
}
