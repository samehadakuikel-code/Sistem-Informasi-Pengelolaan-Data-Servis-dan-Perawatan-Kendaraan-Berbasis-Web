<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('service_date');
            $table->string('service_type');
            $table->unsignedInteger('odometer');
            $table->text('description')->nullable();
            $table->decimal('labor_cost', 14, 2)->default(0);
            $table->decimal('parts_cost', 14, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->date('next_service_date')->nullable();
            $table->unsignedInteger('next_service_odometer')->nullable();
            $table->string('status')->default('Selesai');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('services'); }
};
