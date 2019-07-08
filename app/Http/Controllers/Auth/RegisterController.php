<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/userlist';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerConfirm(Request $request)
    {
        log::info('register method: ');
        $vali2 = $this->validator($request->all())->validate();
        //log::info('validation obj: ');
        //log::info($vali2);
        //log::info($vali2['email']);

        log::info('request: ');
        log::info($request->all());
        $request->session()->put('key', $request->all());
        /* $request->session()->flash('flashstatus', 'Task was successful!');
        log::info('session: ');
        log::info($request->session()->all()); */
        log::info('session : ' . session('key[name]', 'default'));
        log::info(session('key', 'default'));
        $u = session('key');
        log::info($u['name']);
        //event(new Registered($user = $this->create($request->all())));

        /* comment out as no need to login because registerd by admin */
        //$this->guard()->login($user);

        return view('auth.register_confirm', ['user' => $vali2]);
        /* return $this->registered($request, $user)
                        ?: redirect($this->redirectPath()); */
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return 
     */
    protected function create(Request $request)
    {
        
        log::info('create method: ');

        log::info('request: ');
        log::info($request->all());
        log::info('session: ');
        log::info($request->session()->all());

        //log::info('confirmname: ' . $confirmname);
        //log::info($data['dob']->format('d/m/Y'));
        //log::info(date('Y-m-d', strtotime($data['dob'])));
        /* $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile' => '',
            'type' => $data['type'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'dob' => $data['dob'],
            'create_user_id' => Auth::id(),
            'updated_user_id' => Auth::id(),
        ]);
        log::info('*** created user ***');
        log::info($user);
        event(new Registered($user)); */
        return redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function createConfirm(array $data)
    {
        log::info($data['dob']);
        //log::info($data['dob']->format('d/m/Y'));
        //log::info(date('Y-m-d', strtotime($data['dob'])));
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile' => '',
            'type' => $data['type'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'dob' => $data['dob'],
            'create_user_id' => Auth::id(),
            'updated_user_id' => Auth::id(),
        ]);
        log::info('*** created user ***');
        log::info($user);
        return $user;
    }
}
