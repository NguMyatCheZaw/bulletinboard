<?php

namespace App\Services\User;

use App\Contracts\Dao\User\UserDaoInterface;
use App\Contracts\Services\User\UserServiceInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Log;

class UserService implements UserServiceInterface
{
    private $userDao;

    /**
     * Class Constructor
     * @param OperatorUserDaoInterface
     * @return
     */
    public function __construct(UserDaoInterface $userDao)
    {
        $this->userDao = $userDao;
    }
    /**
     * Store the request data on session to confirm.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function registerConfirm(Request $request)
    {
        // store on session to show at confirm page
        // and if cancel, to be back to create form with input data.
        $request->session()->put('name', $request->input('name'));
        $request->session()->put('email', $request->input('email'));
        $request->session()->put('password', $request->input('password'));
        $request->session()->put('type', $request->input('type'));
        $request->session()->put('phone', $request->input('phone'));
        $request->session()->put('dob', $request->input('dob'));
        $request->session()->put('address', $request->input('address'));

        //save the profile picture and store file name on session
        if ($request->hasFile('profile')) {
            //get image file.
            $uploadedFile = $request->file('profile');
            //get just extension.
            $ext = $uploadedFile->getClientOriginalExtension();
            //make a unique name
            $filename = uniqid() . '.' . $ext;
            //upload the image
            $uploadedFile->storeAs('public/image', $filename);
            //store file name on session.
            $request->session()->put('profile', $filename);
            //store file extension on session.
            $request->session()->put('profile-extension', $ext);
        }
    }

    /**
     * Save new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\User $user
     */
    public function create(Request $request)
    {
        $user = $this->userDao->create();
        log::info('*** created user ***');
        log::info($user);
        //change the profile name by user id
        //make the file name
        $filename = $user->id . '.' . session('profile-extension');
        //rename the profile image
        Storage::move('public\\image\\' . session('profile'), 'public\\image\\' . $filename);
        $update = new User;
        $update->id = $user->id;
        //prevent from overwritten by the model's default value
        $update->type = $user->type;
        $update->profile = $filename;
        $update->updated_at = $user->updated_at;
        $this->userDao->update($update);
        //event(new Registered($user));

        //clear the previous user registration info stored on session.
        $this->clear($request);
        log::info($request->session()->all());
        $user->profile = $filename;
        return $user;
    }

    /**
     * Get user list.
     *
     * @return App\Models\User $users
     */
    public function getUserList()
    {
        return $this->userDao->getList();
    }

    /**
     * Search users.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\User $users
     */
    public function search(Request $request)
    {
        $request->session()->flash('name-search', $request->input('name'));
        $request->session()->flash('email-search', $request->input('email'));
        $request->session()->flash('from-search', $request->input('createdfrom'));
        $request->session()->flash('to-search', $request->input('createdto'));
        return $this->userDao->search($request);
    }

    /**
     * Get user by id.
     *
     * @param \Illuminate\Http\Request $request
     * @param int id
     * @return App\Models\User $user
     */
    public function showProfile(Request $request, $id)
    {
        //clear the previous user info stored on session.
        $this->clear($request);
        $request->session()->forget('new-profile');
        $request->session()->forget('new-profile-extension');
        log::info($request->session()->all());

        $user = $this->userDao->getByID($id);
        return $user;
    }

    /**
     * Handle the old user info on session and get user info.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return
     */
    public function prepareUpdateForm(Request $request, $id)
    {
        //clear the previous user info stored on session.
        $this->clear($request);
        $request->session()->forget('new-profile');
        $request->session()->forget('new-profile-extension');

        $user = $this->userDao->getByID($id);

        //to show DB data in update form for edit
        $request->session()->put('id', $user->id);
        $request->session()->put('name', $user->name);
        $request->session()->put('email', $user->email);
        $request->session()->put('type', $user->type);
        $request->session()->put('phone', $user->phone);
        $request->session()->put('dob', $user->dob);
        $request->session()->put('address', $user->address);
        $request->session()->put('profile', $user->profile);
        log::info($request->session()->all());
    }

    /**
     * Check the validation and store on session to confirm.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function updateConfirm(Request $request)
    {
        //save form data to confirm after edit
        //$request->session()->put('id', $user->id);
        $request->session()->put('name', $request->input('name'));
        $request->session()->put('email', $request->input('email'));
        $request->session()->put('type', $request->input('type'));
        $request->session()->put('phone', $request->input('phone'));
        $request->session()->put('dob', $request->input('dob'));
        $request->session()->put('address', $request->input('address'));

        //save the profile picture and store file name on session
        if ($request->hasFile('profile')) {
            //get image file.
            $uploadedFile = $request->file('profile');
            //get just extension.
            $ext = $uploadedFile->getClientOriginalExtension();
            //make a temporary file name
            $filename = session('id') . '-temp.' . $ext;
            //upload the image
            $uploadedFile->storeAs('public/image', $filename);
            //store file name on session.
            $request->session()->put('new-profile', $filename);
            //store file extension on session.
            $request->session()->put('new-profile-extension', $ext);
        }
        log::info($request->session()->all());
    }

    /**
     * Get the user updated data and update on db.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function update(Request $request)
    {
        //delete the old profile and rename new profile after confirmation success
        //make the file name.
        $filename = session('id') . '.' . session('new-profile-extension');

        $update = new User;
        $update->id = session('id');
        $update->name = session('name');
        $update->email = session('email');
        $update->type = session('type');
        $update->phone = session('phone');
        $update->dob = session('dob');
        $update->address = session('address');
        $update->profile = $filename;
        $update->updated_user_id = Auth::id();

        $this->userDao->update($update);

        if (session()->has('new-profile')) {
            //delete the old image.
            $oldProfile = session('profile');
            Storage::delete("public/image/{$oldProfile}"); //no error if the file is not found.

            //rename the new profile.
            Storage::move('public\\image\\' . session('new-profile'), 'public\\image\\' . $filename);
        }

        //clear the previous user registration info stored on session.
        $this->clear($request);
        $request->session()->forget('new-profile');
        $request->session()->forget('new-profile-extension');
        log::info($request->session()->all());
    }

    /**
     * Make soft delete the user by id.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        $this->userDao->delete($id);
    }

    /**
     * Change the user password.
     *
     * @param string $ipassword
     * @return
     */
    public function changePassword($password)
    {
        $this->userDao->changePassword($password);
    }

    /**
     * Delete the old registraion info on session.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function clear(Request $request)
    {
        $request->session()->forget('id');
        $request->session()->forget('name');
        $request->session()->forget('email');
        $request->session()->forget('password');
        $request->session()->forget('type');
        $request->session()->forget('phone');
        $request->session()->forget('dob');
        $request->session()->forget('address');
        $request->session()->forget('profile');
        $request->session()->forget('profile-extension');
    }

    /**
     * Back to the previous page.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $page
     * @return
     */
    public function back(Request $request, $page)
    {
        if (strcmp($page, 'updateform') == 0) {
            //delete the uploaded image as user do cancel.
            $profile = session('new-profile');
            Storage::delete("public/image/{$profile}");

            //clear the user uploaded profile info on session
            $request->session()->forget('new-profile');
            $request->session()->forget('new-profile-extension');
        } elseif (strcmp($page, 'register') == 0) {
            //delete the uploaded image as user do cancel.
            $profile = session('profile');
            Storage::delete("public/image/{$profile}");
        }
    }

    /**
     * Delete the search keyword on session.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function clearSearch(Request $request)
    {
        $request->session()->forget('name-search');
        $request->session()->forget('email-search');
        $request->session()->forget('from-search');
        $request->session()->forget('to-search');
    }

}
