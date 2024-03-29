<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use Illuminate\Support\Facades\Cache;

class ViewsController extends Controller
{
    public function welcome()
    {
        $notams = Cache::remember('notams', now()->addMinutes(10), function () {
            return Bulletin::orderByDesc('created_at')->take(3)->get();
        });

        return view('welcome', [
            'notams' => $notams,
        ]);
    }
}
