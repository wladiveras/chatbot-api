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
        Schema::disableForeignKeyConstraints();

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('flow_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('connection_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->text('content');
            $table->string('type')->default('text');
            $table->string('origin')->default('user');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
