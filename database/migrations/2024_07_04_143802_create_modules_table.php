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

        Schema::create('attendants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('dni', 10);
            $table->string('password', 100);
            $table->boolean('enabled')->default(true);
            $table->enum('status', ['busy', 'free', 'absent', 'offline'])->default('offline');

            $table->timestamps();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('ip_address', 15);
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->boolean('enabled')->default(true);
            $table->foreignId('room_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('client_type_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unique(['name', 'ip_address']);
            $table->foreignId('attention_profile_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->softDeletes();
        });
        Schema::create('module_attendant_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('attendant_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
        Schema::dropIfExists('module_types');
        Schema::dropIfExists('attendant');
        Schema::dropIfExists('module_attendant_accesses');
    }
};
