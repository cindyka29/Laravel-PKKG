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
        Schema::create('kas', function (Blueprint $table) {
            $table->string("id",36)->primary();
            $table->string("activity_id",36)->nullable();
            $table->text("keterangan")->nullable();
            $table->string("tujuan")->nullable();
            $table->date("date");
            $table->enum("type",["in","out"]);
            $table->timestamps();
            $table->foreign("activity_id")->on("activities")->references("id")->onUpdate("CASCADE")->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
