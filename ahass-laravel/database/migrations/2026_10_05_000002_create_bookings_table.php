<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 16)->unique();
            $table->string('nama_pelanggan', 120);
            $table->string('telepon', 20);
            $table->string('no_polisi', 20);
            $table->string('tipe_motor', 80);
            $table->text('keluhan')->nullable();
            $table->foreignId('package_id')->constrained('packages');
            $table->date('tanggal');
            $table->char('jam', 5);
            $table->enum('status', ['pending', 'confirmed', 'progress', 'done', 'cancelled'])->default('pending');
            $table->unsignedInteger('total')->default(0);
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
