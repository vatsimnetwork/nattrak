<?php

namespace App\Http\Controllers;

use App\Models\CtpBooking;
use Illuminate\Http\Request;

class CtpBookingController extends Controller
{
    public function index()
    {
        return CtpBooking::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cid' => ['required'],
            'flight_level' => ['nullable'],
            'selcal' => ['nullable'],
            'destination' => ['required'],
            'track' => ['nullable'],
            'random_routeing' => ['nullable'],
        ]);

        return CtpBooking::create($data);
    }

    public function show(CtpBooking $ctpBooking)
    {
        return $ctpBooking;
    }

    public function update(Request $request, CtpBooking $ctpBooking)
    {
        $data = $request->validate([
            'cid' => ['required'],
            'flight_level' => ['nullable'],
            'selcal' => ['nullable'],
            'destination' => ['required'],
            'track' => ['nullable'],
            'random_routeing' => ['nullable'],
        ]);

        $ctpBooking->update($data);

        return $ctpBooking;
    }

    public function destroy(CtpBooking $ctpBooking)
    {
        $ctpBooking->delete();

        return response()->json();
    }
}
