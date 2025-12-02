<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pemilik form

            $table->string('session_key')->index(); // ID unik per browser/session
            $table->json('data')->nullable();       // isi form yang tersimpan sementara
            $table->boolean('converted')->default(false); // jadi order beneran atau tidak

            $table->timestamp('last_activity_at')->nullable(); // kapan terakhir ngisi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_sessions');
    }
};
