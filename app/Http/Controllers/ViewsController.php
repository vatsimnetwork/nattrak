<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ViewsController extends Controller
{
    public function welcome()
    {
        $notams = Cache::remember('notams', now()->addMinutes(10), function () {
            return json_decode(Http::timeout(5)->get('https://gist.githubusercontent.com/liessdow/78c35cbdeeb97add6a721d3d6b6f0078/raw'));
        });

        return view('welcome', [
            'notams' => $notams,
        ]);
    }
}
