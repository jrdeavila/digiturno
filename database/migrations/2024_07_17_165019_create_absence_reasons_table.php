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
        Schema::create('absence_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('attendant_absence_reason', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendant_id')->constrained()->onDelete('cascade');
            $table->foreignId('absence_reason_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_reasons');
        Schema::dropIfExists('attendant_absence_reason');
    }
};
