<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ViewsController extends Controller
{
    public function welcome()
    {
        $notams = Cache::remember('notams', now()->addMinutes(10), function () {
            return json_decode(Http::timeout(5)->get('https://ganderoceanicoca.ams3.digitaloceanspaces.com/resources/data/nattrak/notams.json'));
        });
        return view('welcome', [
            'notams' => $notams
        ]);
    }
}
