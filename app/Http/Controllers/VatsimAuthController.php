<?php

namespace App\Http\Controllers;

use App\Models\VatsimAccount;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VatsimAuthController extends Controller
{
    public function deauthenticate()
    {
        Auth::logout();
        Session::flush();
        toastr()->info('Logged out');

        return redirect()->route('welcome');
    }

    public function redirect()
    {
        //Prepare the session
        Session::forget(['state', 'token']);
        Session::put('state', $state = Str::random(40));

        //Build request
        $request = http_build_query([
            'client_id' => config('services.vatsim.auth.client_id'),
            'redirect_uri' => config('services.vatsim.auth.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'full_name vatsim_details',
            'required_scopes' => 'vatsim_details',
            'state' => $state,
        ]);

        //Send to VATSIM
        return redirect(config('services.vatsim.auth.endpoint').'/oauth/authorize?'.$request);
    }

    public function authenticate(Request $request)
    {
        //Create cilent
        $http = new Client();

        //Get token
        try {
            $tokenJson = $http->post(config('services.vatsim.auth.endpoint').'/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('services.vatsim.auth.client_id'),
                    'client_secret' => config('services.vatsim.auth.secret'),
                    'redirect_uri' => config('services.vatsim.auth.redirect_uri'),
                    'code' => $request->code,
                ],
            ]);
            Session::put('token', json_decode((string) $tokenJson->getBody(), true));
        } catch (ClientException $ex) {
            Log::alert($ex);
            toastr()->error($ex->getMessage());

            return redirect()->route('welcome');
        }

        //Get user data JSON
        try {
            $userDataJson = $http->get(config('services.vatsim.auth.endpoint').'/api/user', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.Session::get('token.access_token'),
                ],
            ]);
        } catch (ClientException $ex) {
            Log::alert($ex);
            toastr()->error($ex->getMessage());

            return redirect()->route('welcome');
        }

        //Create user data object
        $userData = json_decode($userDataJson->getBody());

        //Create or update the account
        $user = VatsimAccount::updateOrCreate(
            ['id' => $userData->data->cid],
            [
                'given_name' => $userData->data->personal->name_first ?? $userData->data->cid,
                'surname' => $userData->data->personal->name_last ?? $userData->data->cid,
                'rating_int' => $userData->data->vatsim->rating->id,
            ]
        );

        //Login user
        Auth::login($user, false);

        //Redirect
        flashAlert(type: 'success', title: 'Hello!', message: null, toast: true, timer: true);
        if ($request->session()->exists('intended')) {
            return redirect($request->session()->get('intended'));
        } else {
            return redirect()->route('welcome');
        }
    }
}
