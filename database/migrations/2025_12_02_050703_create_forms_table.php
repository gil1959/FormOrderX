<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik form (client)
            $table->string('name');                // nama campaign / form
            $table->string('slug')->unique();      // buat URL internal
            $table->string('embed_token')->unique(); // kunci untuk script embed
            $table->text('description')->nullable();
            $table->decimal('base_price', 15, 2)->nullable(); // harga utama (opsional)
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();  // anti-spam, pixel, dll per form
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
