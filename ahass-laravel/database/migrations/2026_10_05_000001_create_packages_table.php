<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['servis', 'part']);
            $table->string('nama', 120);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('harga')->default(0);
            $table->unsignedSmallInteger('durasi_menit')->default(60);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
