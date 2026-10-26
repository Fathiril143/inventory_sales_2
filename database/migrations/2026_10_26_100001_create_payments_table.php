<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('payment_channel_id')->nullable()->constrained('payment_channels')->nullOnDelete();
            $table->string('reference_number', 50)->unique();
            $table->string('payment_method', 30)->index(); // va, qris, ewallet, manual_transfer, cash
            $table->string('payment_channel', 50)->index(); // BCA, BSI, QRIS, GoPay, etc.
            $table->string('virtual_account_number', 50)->nullable();
            $table->text('qris_payload')->nullable();
            $table->decimal('amount', 14, 2);
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->integer('unique_code')->default(0);
            $table->decimal('total_amount', 14, 2);
            $table->string('proof_of_payment', 255)->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, waiting_verification, paid, expired, failed
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('expired_at')->nullable()->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
