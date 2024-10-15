<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use Illuminate\Support\Facades\Cache;
use Rawilk\Settings\Facades\Settings;

class ViewsController extends Controller
{
    public function welcome()
    {
        if (!auth()->check()) {
            return view('login');
        }

        $selectedMode = auth()->user()->settings()->get('user-mode', 'none');
        if ($selectedMode == 'none') {
            return redirect()->route('auth.mode.select');
        }

        $notams = Cache::remember('notams', now()->addMinutes(10), function () {
            return Bulletin::orderByDesc('created_at')->take(3)->get();
        });

        return view('welcome', [
            'notams' => $notams,
        ]);
    }
}
