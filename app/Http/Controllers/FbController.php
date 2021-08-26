<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use Validator;
use Socialite;
use Exception;
use Auth;
use GuzzleHttp\Client;

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
        $response = $client->request('GET', 'https://ob.nordigen.com/api/aspsps/?country=gb', [
            'headers' => [
                'accept' => 'application/json',
                'Authorization' => 'Token c8bd833f2f02b443cf8593116dbce9e82578340c',
            ]
        ]);

        // $statusCode = $response->getStatusCode();
	    $body = $response->getBody()->getContents();
        return $body;
    }
}
