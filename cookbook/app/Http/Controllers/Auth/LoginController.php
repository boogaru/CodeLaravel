<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;
use Socialite;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function redirectToFacebook()
    {
        return Socialite::with('facebook')->redirect();
    }


    public function getFacebookCallback()
    {
        $data = Socialite::with('facebook')->user();
        $user = User::where('email', $data->email)->first();

        if (!is_null($user)){
            Auth::login($user);
            $user->name = $data->user['name'];
            $user->facebook_id = $data->id;
            $user->save();
        } else{
            $user = User::where('facebook_id', $data->id)->first();
            if(is_null($user)){
                // create a new user
                $user = new User();
                $user->name = $data->user['name'];
                $user->email = $data->email;
                $user->facebook_id = $data->id;
                $user->save();
            }

            Auth::login($user);
        }
        return redirect('/')->with('success','Successfully logged in!');
    }
}
