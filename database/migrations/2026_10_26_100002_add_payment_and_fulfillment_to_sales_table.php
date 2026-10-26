<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('pending')->after('status')->index(); // pending, waiting_verification, paid, expired, failed
            $table->string('payment_method', 30)->nullable()->after('payment_status');
            $table->string('payment_channel', 50)->nullable()->after('payment_method');
            $table->string('fulfillment_status', 30)->default('unfulfilled')->after('payment_channel')->index(); // unfulfilled, processing, shipped, delivered
            $table->timestamp('fulfilled_at')->nullable()->after('fulfillment_status');
            $table->foreignId('fulfilled_by')->nullable()->after('fulfilled_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['fulfilled_by']);
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'payment_channel',
                'fulfillment_status',
                'fulfilled_at',
                'fulfilled_by',
            ]);
        });
    }
};
