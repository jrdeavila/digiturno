<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FindAvailableModuleUtilTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_find_available_module_case_one_free(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $room = \App\Models\Room::factory()->create();
        $attendants = \App\Models\Attendant::factory(4)->create([
            'status' => 'busy',
        ]);
        $attendants[0]->update(['status' => 'free']);

        $modules = \App\Models\Module::factory(4)->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'status' => \App\Enums\ModuleStatus::Online,
        ]);

        $modules->each(function ($module) {
            \App\Models\Shift::factory(5)->create([
                'module_id' => $module->id,
                'state' => \App\Enums\ShiftState::Qualified,
            ]);
        });
        \App\Models\Shift::factory(5)->create([
            'module_id' => $modules[0]->id,
            'state' => \App\Enums\ShiftState::Qualified,
        ]);

        for ($i = 0; $i < 4; $i++) {
            \App\Models\ModuleAttendantAccess::create([
                'module_id' => $modules[$i]->id,
                'attendant_id' => $attendants[$i]->id,
            ]);
        }

        $findModule = \App\Utils\FindAvailableModuleUtil::findModule($room->id, $attentionProfile->id);
        $this->assertEquals(
            $findModule->currentAttendant()->id,
            $attendants[0]->id,
        );
    }

    public function test_find_available_module_case_all_busy(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $room = \App\Models\Room::factory()->create();
        $attendants = \App\Models\Attendant::factory(4)->create([
            'status' => 'busy',
        ]);

        $modules = \App\Models\Module::factory(4)->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'status' => \App\Enums\ModuleStatus::Online,
        ]);

        $modules->each(function ($module) {
            \App\Models\Shift::factory(5)->create([
                'module_id' => $module->id,
                'state' => \App\Enums\ShiftState::Qualified,
            ]);
        });

        for ($i = 0; $i < 4; $i++) {
            \App\Models\ModuleAttendantAccess::create([
                'module_id' => $modules[$i]->id,
                'attendant_id' => $attendants[$i]->id,
            ]);
        }

        $findModule = \App\Utils\FindAvailableModuleUtil::findModule($room->id, $attentionProfile->id);
        $this->assertEquals(
            $findModule->currentAttendant()->id,
            $attendants[0]->id,
        );
    }
}
