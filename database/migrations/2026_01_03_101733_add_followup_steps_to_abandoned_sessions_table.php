<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('abandoned_sessions', function (Blueprint $table) {
            $table->timestamp('followup1_sent_at')->nullable()->after('last_followup_at');
            $table->timestamp('followup2_sent_at')->nullable()->after('followup1_sent_at');
            $table->timestamp('followup3_sent_at')->nullable()->after('followup2_sent_at');
            $table->timestamp('followup4_sent_at')->nullable()->after('followup3_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('abandoned_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'followup1_sent_at',
                'followup2_sent_at',
                'followup3_sent_at',
                'followup4_sent_at',
            ]);
        });
    }
};
