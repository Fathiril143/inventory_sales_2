<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('category', 30)->index(); // qris, va, ewallet, manual_transfer, cash
            $table->string('account_number', 60)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->json('instructions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_channels');
    }
};
