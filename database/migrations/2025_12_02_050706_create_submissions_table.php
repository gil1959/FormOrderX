<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik (client)

            $table->enum('status', ['pending', 'processed', 'completed', 'cancelled'])
                ->default('pending'); // buat dashboard order

            $table->decimal('total_price', 15, 2)->nullable(); // kalau mau hitung total harga
            $table->json('data'); // semua jawaban form (nama, WA, alamat, dll)

            $table->string('source_url')->nullable(); // URL LP yang pakai embed
            $table->string('client_ip', 45)->nullable(); // untuk limit per IP, dsb
            $table->text('user_agent')->nullable();      // untuk deteksi bot
            $table->boolean('is_spam')->default(false);  // hasil filter anti-spam

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
