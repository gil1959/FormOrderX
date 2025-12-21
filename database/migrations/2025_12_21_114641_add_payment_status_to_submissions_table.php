<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])
                ->default('unpaid')
                ->after('status');

            $table->index(['status', 'payment_status']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['submissions_status_payment_status_index']);
            $table->dropIndex(['submissions_submitted_at_index']);
            $table->dropColumn('payment_status');
        });
    }
};
