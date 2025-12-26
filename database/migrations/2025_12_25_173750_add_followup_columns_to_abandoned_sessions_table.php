<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('abandoned_sessions', function (Blueprint $table) {
            $table->timestamp('followup_sent_at')->nullable()->after('last_activity_at');
            $table->string('last_followup_key', 32)->nullable()->after('followup_sent_at');
            $table->timestamp('last_followup_at')->nullable()->after('last_followup_key');
        });
    }

    public function down(): void
    {
        Schema::table('abandoned_sessions', function (Blueprint $table) {
            $table->dropColumn(['followup_sent_at', 'last_followup_key', 'last_followup_at']);
        });
    }
};
