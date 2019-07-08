<?php

namespace App\Contracts\Services\User;

use App\Models\User;
use Illuminate\Http\Request;

interface UserServiceInterface
{
    //store the request data on session to confirm
    public function registerConfirm(Request $request);
    //save new user
    public function create(Request $request);
    //get user list
    public function getUserList();
    //search users
    public function search(Request $request);
    //get user by id
    public function showProfile(Request $request, $id);
    //handle the old user info on session and get user info
    public function prepareUpdateForm(Request $request, $id);
    //update user information
    public function update(Request $request);
    //soft delete user by id
    public function delete($id);
    //change the user password
    public function changePassword($password);
    //delete the old registraion info on session
    public function clear(Request $request);
    //back to the previous page
    public function back(Request $request, $page);
}
