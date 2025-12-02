<?php

namespace App\Http\Controllers\UI\Attention;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            $modules = $user->modules()->get();
            $currentModule = null;
            if ($request->get('module')) {
                $currentModule = $modules->where('id', $request->get('module'))->firstOrFail();
                $currentModule->resetStatus();
                if ($currentModule->moduleType->id === 3) {
                    return redirect()->route('attention.customer-reception.index');
                }
                if ($currentModule->moduleType->id === 4) {
                    return redirect()->route('attention.screen.index', ['room' => $currentModule->room]);
                }
            }
            return view('attention.attention.index', [
                'modules' => $modules,
                'user' => $user,
                'currentModule' => $currentModule,
            ]);
        } catch (ItemNotFoundException $e) {
            return redirect()->back()->with('error', 'No se pudo encontrar el modulo');
        }
    }
}
