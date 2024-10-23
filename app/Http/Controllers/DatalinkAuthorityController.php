<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatalinkAuthorityRequest;
use App\Models\DatalinkAuthority;

class DatalinkAuthorityController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', DatalinkAuthority::class);

        return view('administration.datalink_authorities');
    }
}
