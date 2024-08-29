<?php

namespace App\Utils;

abstract class FindAvailableModuleUtil
{
  public static function findModule(
    int $roomId,
    int $attentionProfileId,
    int $currentModuleId = null,
  ): \App\Models\Module {
    $availableModules = \App\Models\Module::where('enabled', true)
      ->where('room_id', $roomId)
      ->where('id', '!=', $currentModuleId)
      ->where('attention_profile_id', $attentionProfileId)
      ->where('status', \App\Enums\ModuleStatus::Online)->get();

    $freeOrBusyModules = [];
    foreach ($availableModules as $module) {
      if ($module->currentAttendant() !== null && $module->currentAttendant()->status !== 'absent') {
        $freeOrBusyModules[] = $module;
      }
    }

    $selectedModule = null;
    if (count($freeOrBusyModules) > 0) {
      $freeModules = [];
      $busyModules = [];
      foreach ($freeOrBusyModules as $module) {
        if ($module->currentAttendant()->status === 'free') {
          $freeModules[] = $module;
        }
        if ($module->currentAttendant()->status === 'busy') {
          $busyModules[] = $module;
        }
      }

      // Order by shift count and assign the module with the least amount of shifts
      if (count($freeModules) > 0) {
        $selectedModule = $freeModules[0];
        foreach ($freeModules as $module) {
          if (
            $module->shifts()
            ->whereIn('state', [
              'pending',
              'pending-transferred',
            ])
            ->whereDate(
              'created_at',
              now()->toDateString(),
            )->count() < $selectedModule->shifts()
            ->whereIn('state', [
              'pending',
              'pending-transferred',
            ])
            ->whereDate(
              'created_at',
              now()->toDateString(),
            )->count()
          ) {
            $selectedModule = $module;
          }
        }
      }
      if ($selectedModule === null && count($busyModules) > 0) {
        // If there are no free modules, assign the module with the least amount of shifts
        $selectedModule = $busyModules[0];
        foreach ($busyModules as $module) {
          if (
            $module->shifts()
            ->whereIn('state', [
              'pending',
              'pending-transferred',
            ])
            ->whereDate(
              'created_at',
              now()->toDateString(),
            )->count() < $selectedModule->shifts()
            ->whereIn('state', [
              'pending',
              'pending-transferred',

            ])
            ->whereDate(
              'created_at',
              now()->toDateString(),
            )->count()
          ) {
            $selectedModule = $module;
          }
        }
      }
    }
    if ($selectedModule === null) {
      throw new \App\Exceptions\NoAvailableModuleException();
    }
    return $selectedModule;
  }
}
