<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;

class TracksController extends Controller
{
    public function index()
    {
        return view('tracks.index', [
            'activeTracks' => Track::active()->orderBy('identifier')->get(),
            'inactiveTracks' => Track::whereActive(false)->whereConcorde(false)->orderBy('identifier')->get(),
            'concordeTracks' => Track::concorde()->orderBy('identifier')->get(),
            '_pageTitle' => 'Tracks',
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Track $track)
    {
        //
    }

    public function edit(Track $track)
    {
        //
    }

    public function update(Request $request, Track $track)
    {
        //
    }

    public function destroy(Track $track)
    {
        //
    }
}
