<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('keahlian')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->bigInteger('estimasi_biaya');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('technician_id')->nullable()->after('user_id');
            $table->foreignId('service_type_id')->nullable()->after('technician_id');
            $table->bigInteger('estimasi_biaya')->nullable()->after('service_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['technician_id', 'service_type_id', 'estimasi_biaya']);
        });
        Schema::dropIfExists('service_types');
        Schema::dropIfExists('technicians');
    }
};
