<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('followup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('subject_type', 16); // 'submission' | 'abandoned'
            $table->unsignedBigInteger('subject_id');

            $table->string('channel', 16)->default('whatsapp'); // whatsapp|sms|call
            $table->string('key', 32); // welcome|fu1..fu4|wa_processing|sms|call|abandoned
            $table->string('phone', 32)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followup_logs');
    }
};
