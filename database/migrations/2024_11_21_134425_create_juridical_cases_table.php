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
        Schema::create('juridical_cases', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->string('subject');
            $table->foreignId('client_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('attendant_id')
                ->constrained('attendants')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->timestamps();
        });

        Schema::create("juridical_case_observations", function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('juridical_case_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId("attendant_id")
                ->constrained("attendants")
                ->onUpdate("cascade")
                ->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juridical_cases');
    }
};
