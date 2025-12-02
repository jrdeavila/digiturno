<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Module;
use App\Models\Shift;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $clientCount = Client::all()->count();
        $completedShiftCountToDay = Shift::toDay()->qualified()->count();
        $pendingShiftCountToDay = Shift::toDay()->pending()->count();
        $cancelledShiftCountToDay = Shift::toDay()->cancelled()->count();
        $distractedShiftCountToDay = Shift::toDay()->distracted()->count();
        $groupedShiftByBranch = Shift::toDay()->current()->with('room.branch')->get()->groupBy('room.branch.name');
        $groupedShiftByBranch = $groupedShiftByBranch->map(function ($item) {
            return $item->count();
        })->toArray();
        $principalShiftByModule = $this->getPrincipalShiftByModule();
        $groupedShiftByBranch = $this->getGroupedShiftByBranch();
        return view('home', compact(
            'clientCount',
            'completedShiftCountToDay',
            'pendingShiftCountToDay',
            'cancelledShiftCountToDay',
            'distractedShiftCountToDay',
            'groupedShiftByBranch',
            'principalShiftByModule'
        ));
    }

    private function getPrincipalShiftByModule(): array
    {
        $branch = Branch::where('name', 'Principal')->firstOrFail();
        $modules = Module::whereHas('room', function ($query) use ($branch) {
            $query->where('branch_id', $branch->id);
        })->where('module_type_id', 1)->get();
        $principalShiftByModule = [];
        foreach ($modules as $module) {
            $principalShiftByModule[$module->room->name][$module->name] =
                Shift::toDay()->qualified()->whereHas('module', function ($query) use ($module) {
                    $query->where('modules.id', $module->id);
                })->count();
        }
        return $principalShiftByModule;
    }

    private function getGroupedShiftByBranch(): array
    {
        $branches = Branch::orderBy('name')->get();
        $groupedShiftByBranch = [];
        foreach ($branches as $branch) {
            $groupedShiftByBranch[$branch->name] = Shift::toDay()->qualified()->whereHas('room', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })->count();
        }
        return $groupedShiftByBranch;
    }
}
