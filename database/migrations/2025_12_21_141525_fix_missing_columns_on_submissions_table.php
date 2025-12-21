<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'payment_status')) {
                $table->string('payment_status')->default('unpaid');
                // kalau lu sebenarnya pakai enum, boleh diganti enum,
                // tapi string aman dulu biar gak error di environment beda-beda
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
