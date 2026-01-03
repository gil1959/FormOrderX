<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('embed_nonces', function (Blueprint $table) {
            $table->id();

            // token embed form (route param {token})
            $table->string('token', 64);

            // sha256(nonce) biar nonce mentah gak disimpan di DB
            $table->string('nonce_hash', 64);

            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();

            $table->timestamps();

            $table->unique(['token', 'nonce_hash']);
            $table->index(['token', 'expires_at']);
            $table->index(['token', 'consumed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embed_nonces');
    }
};
