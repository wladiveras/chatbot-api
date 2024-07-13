<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('lead_id ')->nullable()->index();
            $table->foreignId('product_id')->nullable()->index();
            $table->foreignId('payment_id')->nullable()->index();
            $table->string('fee')->nullable();
            $table->string('amount')->nullable();
            $table->string('total')->nullable();
            $table->integer('last_activity')->index();
            $table->timestamps();
        });

        Schema::create('payment_requests', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('product_id')->nullable()->index();
            $table->longText('payload');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('last_activity')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('payment_requests');
    }
};
