<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('label');      // label yang tampil (Nama Lengkap)
            $table->string('name');       // nama field di HTML (full_name)
            $table->string('type');       // text, textarea, select, number, phone, dll
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // untuk select/radio (pilihan paket, dll)
            $table->unsignedInteger('order')->default(0); // urutan tampil
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
