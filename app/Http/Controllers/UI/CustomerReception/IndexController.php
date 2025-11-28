<?php

namespace App\Http\Controllers\UI\CustomerReception;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\MregEstInscrito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $searched = $request->has('dni');
        $client = null;
        if ($request->dni) {
            $client = Client::where('dni', $request->dni)->first();
            if (!$client) {
                $mreg = MregEstInscrito::where('is_member', 1)->where('is_member_since', now()->year)->where('id_number', $request->dni)->first();
                if ($mreg) {
                    $client = Client::create([
                        'name' => $mreg->name,
                        'dni' => $mreg->id_number,
                        'client_type_id' => 3, // Afiliado
                    ]);
                }
            }
        }
        $user = User::find(Auth::id());
        $clientTypes = ClientType::all();
        $currentRoom = $user->modules->where('module_type_id', 3)->first()->room;
        $attentionProfiles = $currentRoom->attentionProfiles()->distinct()->get();

        // Shifts
        $shifts = $currentRoom->shifts()->pending()->get();
        $distractedShifts = $currentRoom->shifts()->distracted()->paginate(3);

        // Shift metrics
        $toDayCount = $currentRoom->shifts()->toDay()->count();
        $distractedCount = $currentRoom->shifts()->distracted()->count();
        $pendingCount = $currentRoom->shifts()->pending()->count();

        $modules = $currentRoom->modules()->where('module_type_id', 1)->enabled()->with('currentShifts')->orderBy('name', 'asc')->get();
        // Modules
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
            'modules',
            'currentRoom'
        ));
    }
}
