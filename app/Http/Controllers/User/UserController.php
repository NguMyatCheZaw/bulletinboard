<?php

namespace App\Http\Controllers\User;

use App\Contracts\Services\User\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the various user operations as well as their
    | validation.
    | By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    //use RegistersUsers;

    private $userServiceInterface;

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
    public function __construct(UserServiceInterface $userInterface)
    {
        $this->middleware('auth');
        $this->userServiceInterface = $userInterface;
    }

    /**
     * Handle the old registraion info on session and show registration form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function prepareRegisterForm(Request $request)
    {
        //clear the previous user registration info stored on session.
        $this->userServiceInterface->clear($request);
        log::info($request->session()->all());
        return redirect()->route('register');
    }

    /**
     * Check the validation and store on session to confirm.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function registerConfirm(Request $request)
    {
        log::info('confirm method: ');

        Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required',
            'phone' => 'required|numeric',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'profile' => 'required|image|max:1999', //size is specified in kilobytes
        ])->validate();

        //store the request data on session to show at confirm page
        $this->userServiceInterface->registerConfirm($request);
        log::info($request->session()->all());

        return view('auth.register_confirm');
    }

    /**
     * Back to the previous page.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $page
     * @return \Illuminate\Http\Response
     */
    public function back(Request $request, $page)
    {
        log::info('back method: ');

        //delete the uploaded image as user do cancel
        $this->userServiceInterface->back($request, $page);
        return redirect()->route($page);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        log::info('create method: ');
        $user = $this->userServiceInterface->create($request);
        return redirect($this->redirectTo);
    }

    /**
     * Get all users and show as a list.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request)
    {
        log::info('getList method: ');
        $this->userServiceInterface->clearSearch($request);
        $users = $this->userServiceInterface->getUserList();
        log::info($request->session()->all());
        return view('user.userlist', ['users' => $users]);
    }

    /**
     * Validate the input and search users.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'createdfrom' => 'nullable|date',
            'createdto' => 'nullable|date',
        ]);
        $validator->after(function ($validator) use ($request) {
            if ($request->filled('createdfrom') && $request->filled('createdto')) {
                if ($request->input('createdto') <= $request->input('createdfrom')) {
                    //from-date is greater than to-date
                    $validator->errors()->add('createdfrom', '(から)日付は(に)日付より大きくなければなりません。');
                }

            }
        });
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $users = $this->userServiceInterface->search($request);
        log::info($request->session()->all());
        return view('user.userlist', ['users' => $users]);
    }

    /**
     * Show the user profile.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showProfile(Request $request, $id)
    {
        log::info('showProfile method: ');

        $user = $this->userServiceInterface->showProfile($request, $id);

        return view('user.profile', ['user' => $user]);
    }

    /**
     * Handle the old user info on session and show user update form.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function prepareUpdateForm(Request $request, $id)
    {
        log::info('prepareUpdateForm method: ');

        $user = $this->userServiceInterface->prepareUpdateForm($request, $id);
        return view('user.update');
    }

    /**
     * Check the validation and store on session to confirm.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateConfirm(Request $request)
    {
        log::info('confirm method: ');

        Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users,name,' . session('id'),
            'email' => 'required|string|email|max:255|unique:users,email,' . session('id'),
            'type' => 'required',
            'phone' => 'required|numeric',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'profile' => 'nullable|image|max:1999', //size is specified in kilobytes
        ])->validate();

        //save form data to confirm after edit
        $this->userServiceInterface->updateConfirm($request);
        return view('user.update_confirm');
    }

    /**
     * Update the user info after the validation successes.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        log::info('update method: ');

        $this->userServiceInterface->update($request);

        if (Auth::user()->type) {
            return redirect('/profile/' . Auth::id());
        } else {
            return redirect($this->redirectTo);
        }
    }

    /**
     * Delete the user.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        log::info('delete method: ' . $id);
        $this->userServiceInterface->delete($id);

        return redirect()->back();
    }

    /**
     * Check the validation and if not fail, change the user password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        log::info('change pwd method: ');

        $validator = Validator::make($request->all(), [
            'current-password' => ['required', 'string'],
            'new-password' => ['required', 'string', 'min:8'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!(Hash::check($request->input('current-password'), Auth::user()->password))) {
                $validator->errors()->add('current-password', 'Yor current password does not match.');
            } elseif (strcmp($request->input('current-password'), $request->input('new-password')) == 0) {
                //Current password and new password are same
                $validator->errors()->add('new-password', 'Current and new password should not be same. Please choose a different password.');
            }

            if (strcmp($request->input('new-password'), $request->input('new-password-confirmation')) != 0) {
                //Current password and new password are same
                $validator->errors()->add('new-password', 'The new password confirmation does not match.');
            }
        });
        if ($validator->fails()) {
            //log::info($validator->errors());
            return redirect()->back()->withErrors($validator);
        }

        $this->userServiceInterface->changePassword($request->input('new-password'));

        if (Auth::user()->type) {
            return redirect('/profile/' . Auth::id());
        } else {
            return redirect($this->redirectTo);
        }
    }

}
