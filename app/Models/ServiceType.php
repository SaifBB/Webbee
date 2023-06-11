<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    public function serviceSettings()
    {
        return $this->hasMany(SeviceSetting::class);
    }
}
