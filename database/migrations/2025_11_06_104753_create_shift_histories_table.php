<?php

use App\Enums\ShiftState;
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
        Schema::create('shift_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')
                ->constrained('shifts')
                ->cascadeOnDelete();
            $table->enum('state', array_keys(ShiftState::cases()));
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_histories');
    }
};
