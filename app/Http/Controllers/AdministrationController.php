<?php

namespace App\Http\Controllers;

use App\Enums\AccessLevelEnum;
use App\Models\ClxMessage;
use App\Models\CpdlcMessage;
use App\Models\RclMessage;
use App\Models\VatsimAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdministrationController extends Controller
{
    public function index()
    {
        return redirect()->route('administration.accounts');
    }

    public function accounts()
    {
        return view('administration.accounts.index');
    }

    public function controllers()
    {
        return view('administration.accounts.controllers');
    }

    public function addAccess(Request $request)
    {
        $vatsimAccount = VatsimAccount::whereId($request->get('id'))->first();

        if (! $vatsimAccount) {
            flashAlert(type: 'error', title: 'Account not found. They may need to login to natTrak first.', message: null, toast: false, timer: false);

            return redirect()->route('administration.accounts');
        }

        $vatsimAccount->access_level = AccessLevelEnum::Administrator;
        $vatsimAccount->save();

        flashAlert(type: 'success', title: 'Account added', message: null, toast: true, timer: true);

        return redirect()->route('administration.accounts');
    }

    public function addControllerAccess(Request $request)
    {
        $vatsimAccount = VatsimAccount::whereId($request->get('id'))->first();

        if (! $vatsimAccount) {
            flashAlert(type: 'error', title: 'Account not found. They may need to login to natTrak first.', message: null, toast: false, timer: false);

            return redirect()->route('administration.controllers');
        } elseif ($vatsimAccount->can('administrate')) {
            flashAlert(type: 'error', title: 'Account already an administrator.', message: null, toast: false, timer: false);

            return redirect()->route('administration.controllers');
        }

        $vatsimAccount->access_level = AccessLevelEnum::Controller;
        $vatsimAccount->save();

        flashAlert(type: 'success', title: 'Account added', message: null, toast: true, timer: true);

        return redirect()->route('administration.controllers');
    }

    public function removeAccess(Request $request)
    {
        $vatsimAccount = VatsimAccount::whereId($request->get('vatsimAccountId'))->first();

        if (Auth::id() == $vatsimAccount->id) {
            flashAlert(type: 'error', title: 'You can\'t remove yourself!', message: null, toast: false, timer: false);

            return redirect()->route('administration.accounts');
        } elseif ($vatsimAccount->access_level == AccessLevelEnum::Root) {
            flashAlert(type: 'error', title: 'You can\'t remove a root user.', message: null, toast: false, timer: false);

            return redirect()->route('administration.accounts');
        }

        if ($vatsimAccount->rating_int < 5) {
            $vatsimAccount->access_level = AccessLevelEnum::Pilot;
        } else {
            $vatsimAccount->access_level = AccessLevelEnum::Controller;
        }

        $vatsimAccount->save();

        flashAlert(type: 'info', title: "$vatsimAccount->id's access has been removed.", message: null, toast: false, timer: false);

        return redirect()->route('administration.accounts');
    }

    public function removeControllerAccess(Request $request)
    {
        $vatsimAccount = VatsimAccount::whereId($request->get('vatsimAccountId'))->first();

        if (Auth::id() == $vatsimAccount->id) {
            flashAlert(type: 'error', title: 'You can\'t remove yourself!', message: null, toast: false, timer: false);

            return redirect()->route('administration.controllers');
        }

        $vatsimAccount->access_level = AccessLevelEnum::Pilot;
        $vatsimAccount->save();

        flashAlert(type: 'info', title: "$vatsimAccount->id's access has been removed.", message: null, toast: false, timer: false);

        return redirect()->route('administration.controllers');
    }

    public function activityLog()
    {
        return view('administration.activity-log');
    }

    public function utility()
    {
        return view('administration.utility', [
            'countRcl' => RclMessage::count(),
            'countClx' => ClxMessage::count(),
            'countCpdlc' => CpdlcMessage::count()
        ]);
    }

    public function clearDb(Request $request)
    {
        if ($request->user()->access_level != AccessLevelEnum::Root) {
            flashAlert(type: 'error', title: 'Insufficient permissions', message: null, toast: false, timer: false);
            return redirect()->route('administration.utility');
        }
        
        try {
            CpdlcMessage::query()->delete();
            ClxMessage::query()->delete();
            RclMessage::query()->delete();
        }
        catch (\Exception $e) {
            flashAlert(type: 'error', title: $e->getMessage(), message: null, toast: false, timer: false);
            Log::error($e);
            return redirect()->route('administration.utility');
        }

        flashAlert(type: 'success', title: 'Cleared', message: null, toast: true, timer: true);

        return redirect()->route('administration.utility');
    }
}
