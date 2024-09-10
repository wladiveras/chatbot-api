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
        Schema::table('flows', function (Blueprint $table) {
            $table->bigInteger('recovery_flow_id')->nullable();
            $table->bigInteger('finished_flow_id')->nullable();
            $table->integer('recovery_days')->nullable();
            $table->integer('finished_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flows', function (Blueprint $table) {
            $table->dropColumn('recovery_flow_id');
            $table->dropColumn('finished_flow_id');
            $table->dropColumn('recovery_days');
            $table->dropColumn('finished_days');
        });
    }
};
