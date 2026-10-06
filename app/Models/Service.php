<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['vehicle_id', 'service_date', 'service_type', 'odometer', 'description', 'labor_cost', 'parts_cost', 'total_cost', 'next_service_date', 'next_service_odometer', 'status'];
    protected $casts = ['service_date' => 'date', 'next_service_date' => 'date', 'labor_cost' => 'decimal:2', 'parts_cost' => 'decimal:2', 'total_cost' => 'decimal:2'];
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function parts() { return $this->hasMany(ServicePart::class); }
}
