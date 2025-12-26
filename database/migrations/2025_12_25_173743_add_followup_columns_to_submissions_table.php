<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('welcome_sent_at')->nullable()->after('payment_status');
            $table->timestamp('followup1_sent_at')->nullable()->after('welcome_sent_at');
            $table->timestamp('followup2_sent_at')->nullable()->after('followup1_sent_at');
            $table->timestamp('followup3_sent_at')->nullable()->after('followup2_sent_at');
            $table->timestamp('followup4_sent_at')->nullable()->after('followup3_sent_at');

            $table->string('last_followup_key', 32)->nullable()->after('followup4_sent_at');
            $table->timestamp('last_followup_at')->nullable()->after('last_followup_key');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn([
                'welcome_sent_at',
                'followup1_sent_at',
                'followup2_sent_at',
                'followup3_sent_at',
                'followup4_sent_at',
                'last_followup_key',
                'last_followup_at',
            ]);
        });
    }
};
