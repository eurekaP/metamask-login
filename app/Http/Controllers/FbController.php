<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use Validator;
use Socialite;
use Exception;
use Auth;
use GuzzleHttp\Client;

use function GuzzleHttp\json_decode;

class FbController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookSignin()
    {
        try {

            $user = Socialite::driver('facebook')->user();
            $facebookId = User::where('facebook_id', $user->id)->first();

            if($facebookId){
                Auth::login($facebookId);
                return redirect('/dashboard');
            }else{
                $createUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'facebook_id' => $user->id,
                    'password' => encrypt('john123')
                ]);

                Auth::login($createUser);
                return redirect('/dashboard');
            }

        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function metamaskSignin() {
        $createUser = User::create([
            'name' => $_GET['address'],
            'email' => (rand() % 10000).'@gmail.com',
            'password' => 'john123'
        ]);

        Auth::login($createUser);
        return redirect('/dashboard');
    }

    public function revolutSignin() {

        $client = new Client();
        $url = 'https://ob.nordigen.com/api/agreements/enduser/';
        $data = [
            'max_historical_days' => 30,
            'enduser_id' => '8234e18b-f360-48cc-8bcf-c8625596d74a',
            'aspsp_id' => 'REVOLUT_REVOGB21'
        ];
        $request = $client->post($url , ['body' => json_encode($data), 'headers' => [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Token c8bd833f2f02b443cf8593116dbce9e82578340c'],
        ]);

	    $body = json_decode($request->getBody());

        $redirect = 'http://localhost:8000/';
        $reference = '124158';
        $enduser_id = $body->enduser_id;
        $agreements = array($body->id);
        $user_language = 'EN';

        $url = 'https://ob.nordigen.com/api/requisitions/';
        $data = [
            'redirect' => $redirect,
            'reference' => $reference,
            'enduser_id' => $enduser_id,
            'agreements' => $agreements,
            'user_language' => $user_language,
        ];

        $request = $client->post($url , ['body' => json_encode($data), 'headers' => [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Token c8bd833f2f02b443cf8593116dbce9e82578340c'],
        ]);

        $body = json_decode($request->getBody());

        $requisition_id = $body->id;

        $url = 'https://ob.nordigen.com/api/requisitions/'.$requisition_id.'/'.'links'.'/';
        $data = [
            'aspsp_id' => 'REVOLUT_REVOGB21',
        ];

        $request = $client->post($url , ['body' => json_encode($data), 'headers' => [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Token c8bd833f2f02b443cf8593116dbce9e82578340c'],
        ]);

        $body = json_decode($request->getBody());

        $initiate = $body->initiate;

        var_dump($initiate);

        // $request = $client->get($initiate);

        // $request->getBody();
    }
}
