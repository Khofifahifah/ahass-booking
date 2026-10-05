<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages');
            $table->unsignedSmallInteger('qty')->default(1);
            $table->unsignedInteger('harga');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
