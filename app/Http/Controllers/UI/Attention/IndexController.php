<?php

namespace App\Http\Controllers\UI\Attention;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::find(Auth::id());
        $modules = $user->modules()->get();
        $currentModule = null;
        if ($request->get('module')) {
            $currentModule = $modules->where('id', $request->get('module'))->firstOrFail();
            $currentModule->resetStatus();
            if ($currentModule->moduleType->id === 3) {
                return redirect()->route('attention.customer-reception.index');
            }
        }
        return view('attention.attention.index', [
            'modules' => $modules,
            'user' => $user,
            'currentModule' => $currentModule,
        ]);
    }
}
