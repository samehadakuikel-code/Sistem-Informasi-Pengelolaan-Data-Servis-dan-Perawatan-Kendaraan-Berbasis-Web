<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = ['plate_number', 'brand', 'model', 'year', 'owner_name', 'owner_phone', 'current_odometer', 'notes'];
    protected $casts = ['year' => 'integer', 'current_odometer' => 'integer'];
    public function services() { return $this->hasMany(Service::class); }
    public function getDisplayNameAttribute(): string { return "{$this->plate_number} - {$this->brand} {$this->model}"; }
}
