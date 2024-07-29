<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flow_session_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flow_session_metas');
    }
};
