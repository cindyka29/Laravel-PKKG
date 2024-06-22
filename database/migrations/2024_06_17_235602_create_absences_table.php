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
        Schema::create('absences', function (Blueprint $table) {
            $table->string('id',36)->primary();
            $table->string('user_id',36)->nullable();
            $table->string('activity_id',36)->nullable();
            $table->boolean('is_attended');
            $table->timestamps();
            $table->foreign("user_id")->on("users")->references("id")->onUpdate("CASCADE")->onDelete("SET NULL");
            $table->foreign("activity_id")->on("activities")->references("id")->onUpdate("CASCADE")->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
