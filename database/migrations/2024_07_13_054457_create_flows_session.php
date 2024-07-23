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

        Schema::create('flow_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')->constrained()->onDelete('cascade');
            $table->foreignId('connection_id')->constrained()->onDelete('cascade');
            $table->string('session_key')->nullable();
            $table->integer('step')->default(1);
            $table->integer('is_running')->default(0);
            $table->string('country')->default('BR');
            $table->timestamp('last_active')->useCurrent();
            $table->timestamp('session_start')->useCurrent();
            $table->timestamp('session_end')->nullable();

            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flows_sessions');
    }
};
