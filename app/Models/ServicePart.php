<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePart extends Model
{
    protected $fillable = ['name', 'quantity', 'unit_price', 'subtotal'];
    protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];
    public function service() { return $this->belongsTo(Service::class); }
}
