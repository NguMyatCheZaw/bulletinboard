<?php

namespace App\Services\User;

use App\Contracts\Dao\User\UserDaoInterface;
use App\Contracts\Services\User\UserServiceInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    public function registerConfirm($request)
    {
        // store on session to show at confirm page
        // and if cancel, to be back to create form with input data.
        $registerUser = new User;
        $registerUser->name = $request->input('name');
        $registerUser->email = $request->input('email');
        $registerUser->password = $request->input('password');
        $registerUser->type = $request->input('type');
        $registerUser->phone = $request->input('phone');
        $registerUser->dob = $request->input('dob');
        $registerUser->address = $request->input('address');

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
            $registerUser->profile = $filename;
            $request->session()->put('profile-path', config('constants.profile-path') . $filename);
            //store file extension on session.
            $request->session()->put('profile-extension', $ext);
        }
        $request->session()->put('register-info', $registerUser);
    }

    /**
     * Save new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\User $user
     */
    public function create($request)
    {
        $result = $this->userDao->create();

        if ($result['status'] == -9) {
            return $result;
        }

        $user = $result['data'];

        //change the profile name by user id
        //make the file name
        $filename = $user->id . '.' . session('profile-extension');
        //rename the profile image
        Storage::move('public\\image\\' . session('register-info.profile'), 'public\\image\\' . $filename);
        $update = new User;
        $update->id = $user->id;
        //prevent from overwritten by the model's default value
        $update->type = $user->type;
        $update->profile = $filename;
        $update->updated_at = $user->updated_at;
        $result = $this->userDao->update($update);

        if ($result['status'] == -9) {
            return $result;
        }

        //clear the previous user registration info stored on session.
        $this->clear($request, ['register-info', 'profile-path', 'profile-extension']);

        $user->profile = $filename;
        $result['data'] = $user;

        return $result;
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
    public function search($request)
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
    public function showProfile($request, $id)
    {
        //clear the previous user info stored on session.
        $this->clear($request, ['update-info', 'profile-path', 'new-profile', 'new-profile-path', 'new-profile-extension']);

        $result = $this->userDao->getByID($id);

        return $result;
    }

    /**
     * Handle the old user info on session and get user info.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return
     */
    public function prepareUpdateForm($request, $id)
    {
        //clear the previous user info stored on session.
        $this->clear($request, ['update-info', 'profile-path', 'new-profile', 'new-profile-path', 'new-profile-extension']);

        $result = $this->userDao->getByID($id);

        $user = $result['data'];

        //to show DB data in update form for edit
        $request->session()->put('update-info', $user);
        $request->session()->put('profile-path', config('constants.profile-path') . $user->profile);

        return $result;
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
        $updateinfo = session('update-info');
        $updateinfo->name = $request->input('name');
        $updateinfo->email = $request->input('email');
        $updateinfo->type = $request->input('type');
        $updateinfo->phone = $request->input('phone');
        $updateinfo->dob = $request->input('dob');
        $updateinfo->address = $request->input('address');
        $request->session()->put('update-info', $updateinfo);

        //save the profile picture and store file name on session
        if ($request->hasFile('profile')) {
            //get image file.
            $uploadedFile = $request->file('profile');
            //get just extension.
            $ext = $uploadedFile->getClientOriginalExtension();
            //make a temporary file name
            $filename = session('update-info.id') . '-temp.' . $ext;
            //upload the image
            $uploadedFile->storeAs('public/image', $filename);
            //store file name on session.
            $request->session()->put('new-profile', $filename);
            $request->session()->put('new-profile-path', config('constants.profile-path') . $filename);
            //store file extension on session.
            $request->session()->put('new-profile-extension', $ext);
        }
    }

    /**
     * Get the user updated data and update on db.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function update($request)
    {
        //delete the old profile and rename new profile after confirmation success
        //make the file name.
        $filename = session('update-info.id') . '.' . session('new-profile-extension');
        $update = new User;
        $update->id = session('update-info.id');
        $update->name = session('update-info.name');
        $update->email = session('update-info.email');
        $update->type = session('update-info.type');
        $update->phone = session('update-info.phone');
        $update->dob = session('update-info.dob');
        $update->address = session('update-info.address');
        $update->profile = $filename;
        $update->updated_user_id = Auth::id();

        $result = $this->userDao->update($update);

        if (session()->has('new-profile')) {
            if ($result['status'] == -1) {
                //delete the old image.
                $oldProfile = session('update-info.profile');
                Storage::delete("public/image/{$oldProfile}"); //no error if the file is not found.

                //rename the new profile.
                Storage::move('public\\image\\' . session('new-profile'), 'public\\image\\' . $filename);
            } else {
                Storage::delete("public/image/" . session('new-profile'));
            }
        }

        //clear the previous user registration info stored on session.
        $this->clear($request, ['update-info', 'profile-path', 'new-profile', 'new-profile-path', 'new-profile-extension']);

        return $result;
    }

    /**
     * Make soft delete the user by id.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        return $this->userDao->delete($id);
    }

    /**
     * Change the user password.
     *
     * @param string $ipassword
     * @return
     */
    public function changePassword($password)
    {
        return $this->userDao->changePassword($password);
    }

    /**
     * Back to the previous page.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $page
     * @return
     */
    public function back($request, $page)
    {
        if (strcmp($page, 'updateform') == 0) {
            //delete the uploaded image as user do cancel.
            $profile = "public/image/" . session('new-profile');
            Storage::delete($profile);

            //clear the user uploaded profile info on session
            $this->clear($request, ['new-profile', 'new-profile-path', 'new-profile-extension']);
        } elseif (strcmp($page, 'register') == 0) {
            //delete the uploaded image as user do cancel.
            $profile = "public/image/" . session('register-info.profile');
            Storage::delete($profile);
        }
    }

    /**
     * Delete the old registraion info on session.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $attributeList
     * @return
     */
    public function clear($request, $attributeList)
    {
        foreach ($attributeList as $attribute) {
            session()->forget($attribute);
        }
    }

    /**
     * Delete the search keyword on session.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function clearSearch($request)
    {
        $request->session()->forget('name-search');
        $request->session()->forget('email-search');
        $request->session()->forget('from-search');
        $request->session()->forget('to-search');
    }

}
