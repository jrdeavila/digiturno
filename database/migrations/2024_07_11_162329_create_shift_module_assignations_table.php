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

        Schema::create('shift_module_assignations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('module_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['assigned', 'transferred', 'completed'])->default('assigned');
            $table->timestamps();
        });

        Schema::table('qualifications', function (Blueprint $table) {
            $table->foreignId('shift_module_assignation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_module_assignations');
        Schema::table('qualifications', function (Blueprint $table) {
            $table->dropForeign(['shift_module_assignation_id']);
            $table->dropColumn('shift_module_assignation_id');
        });
    }
};
