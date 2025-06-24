<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TracksApiController extends Controller
{
    public function listTracks(Request $request)
    {
        $tracks = Track::active()->when($request->has('identifier'), function (Builder $query) use ($request) {
            $query->where('identifier', $request->get('identifier'))->firstOrFail();
        })->get();

        return response()->json($tracks);
    }

    public function listAllTracks(Request $request)
    {
        $tracks = Track::when($request->has('identifier'), function (Builder $query) use ($request) {
            $query->where('identifier', $request->get('identifier'))->firstOrFail();
        })->get();

        return response()->json($tracks);
    }
}
