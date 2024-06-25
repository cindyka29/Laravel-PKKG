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
        Schema::table("activities",function (Blueprint $table){
            $table->string("program_id",36)->nullable()->after("date");
            $table->foreign("program_id")->on("programs")->references("id")->onUpdate("CASCADE")->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
