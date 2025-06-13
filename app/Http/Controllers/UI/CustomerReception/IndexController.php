<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\AttentionProfile;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $searched = $request->has('dni');
        $client = Client::where('dni', $request->dni)->first();
        $user = User::find(Auth::id());
        $clientTypes = ClientType::all();
        $currentRoom = $user->modules->where('module_type_id', 3)->first()->room;
        $attentionProfiles = $currentRoom->attentionProfiles;

        // Shifts
        $shifts = $currentRoom->shifts()->pending()->paginate(10);
        $distractedShifts = $currentRoom->shifts()->distracted()->paginate(3);

        // Shift metrics
        $toDayCount = $currentRoom->shifts()->toDay()->count();
        $distractedCount = $currentRoom->shifts()->distracted()->count();
        $pendingCount = $currentRoom->shifts()->pending()->count();

        // Modules
        $modules = $currentRoom->modules()->where('module_type_id', 1)->enabled()->orderBy('name', 'desc')->get();
        return view('attention.customer-reception.index', compact(
            'clientTypes',
            'searched',
            'client',
            'shifts',
            'distractedShifts',
            'attentionProfiles',
            'toDayCount',
            'distractedCount',
            'pendingCount',
            'modules'
        ));
    }
}
