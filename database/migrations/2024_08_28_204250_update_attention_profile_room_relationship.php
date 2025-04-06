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
        $attentionProfiles = \App\Models\AttentionProfile::all();
        Schema::table('attention_profiles', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
        Schema::create('room_has_attention_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attention_profile_id')
                ->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')
                ->constrained()->cascadeOnDelete();
        });

        foreach ($attentionProfiles as $attentionProfile) {
            if (!$attentionProfile->room_id) {
                continue;
            }
            \App\Models\Room::find($attentionProfile->room_id)
                ->attentionProfiles()
                ->attach($attentionProfile->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attention_profiles', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->nullable()
                ->constrained()->cascadeOnDelete();
        });

        $rooms = \App\Models\Room::all();

        foreach ($rooms as $room) {
            foreach ($room->attentionProfiles as $attentionProfile) {
                $attentionProfile->room_id = $room->id;
                $attentionProfile->save();
            }
        }

        Schema::dropIfExists('room_has_attention_profiles');
    }
};
