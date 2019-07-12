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
        $this->userServiceInterface->clear($request, ['register-info', 'profile-path', 'profile-extension']);

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
        $result = $this->userServiceInterface->create($request);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

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
        $this->userServiceInterface->clearSearch($request);
        $result = $this->userServiceInterface->getUserList();

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        $users = $result['data'];

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
                    $validator->errors()->add('createdfrom', config('constants.errors.from_to'));
                }

            }
        });
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $result = $this->userServiceInterface->search($request);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        $users = $result['data'];

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
        $result = $this->userServiceInterface->showProfile($request, $id);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        $user = $result['data'];

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
        $result = $this->userServiceInterface->prepareUpdateForm($request, $id);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

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
        Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users,name,' . session('update-info.id'),
            'email' => 'required|string|email|max:255|unique:users,email,' . session('update-info.id'),
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
        $result = $this->userServiceInterface->update($request);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

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
        $result = $this->userServiceInterface->delete($id);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

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
        $validator = Validator::make($request->all(), [
            'current-password' => ['required', 'string'],
            'new-password' => ['required', 'string', 'min:8'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!(Hash::check($request->input('current-password'), Auth::user()->password))) {
                $validator->errors()->add('current-password', config('constants.errors.password_fail'));
            } elseif (strcmp($request->input('current-password'), $request->input('new-password')) == 0) {
                //Current password and new password are same
                $validator->errors()->add('new-password', config('constants.errors.password_same'));
            }

            if (strcmp($request->input('new-password'), $request->input('new-password-confirmation')) != 0) {
                //Password confirmation fails
                $validator->errors()->add('new-password', config('constants.errors.password_confirm'));
            }
        });
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $result = $this->userServiceInterface->changePassword($request->input('new-password'));

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        if (Auth::user()->type) {
            return redirect('/profile/' . Auth::id());
        } else {
            return redirect($this->redirectTo);
        }
    }

}
