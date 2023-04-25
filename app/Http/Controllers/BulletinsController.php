<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulletinRequest;
use App\Models\Bulletin;

class BulletinsController extends Controller
{
    public function index()
    {
        return view('bulletins.index', [
            'bulletins' => Bulletin::orderByDesc('created_at')->paginate(5),
        ]);
    }

    public function create()
    {
        return view('bulletins.create');
    }

    public function store(BulletinRequest $request)
    {
        $bulletin = new Bulletin($request->all());
        $bulletin->save();
        flashAlert(type: 'success', title: null, message: 'NOTAM created!', toast: true, timer: true);

        return redirect()->route('notams.index');
    }

    public function destroy(Bulletin $bulletin)
    {
        $bulletin->delete();
        flashAlert(type: 'info', title: null, message: 'NOTAM deleted.', toast: true, timer: true);

        return redirect()->route('notams.index');
    }
}
