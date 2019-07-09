<?php

namespace App\Contracts\Services\User;

use App\Models\User;
use Illuminate\Http\Request;

interface UserServiceInterface
{
    /**
     * store the request data on session to confirm
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function registerConfirm($request);

    /**
     * save new user
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\User
     */
    public function create($request);

    /**
     * get user list
     *
     * @param
     * @return App\Models\User
     */
    public function getUserList();

    /**
     * search users
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\User
     */
    public function search($request);

    /**
     * get user by id
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return App\Models\User
     */
    public function showProfile($request, $id);

    /**
     * handle the old user info on session and get user info
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return
     */
    public function prepareUpdateForm($request, $id);

    /**
     * update user information
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function update($request);

    /**
     * soft delete user by id
     *
     * @param int $id
     * @return
     */
    public function delete($id);

    /**
     * change the user password
     *
     * @param string $password
     * @return
     */
    public function changePassword($password);

    /**
     * delete the old registraion info on session
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function clear($request);

    /**
     * back to the previous page
     *
     * @param \Illuminate\Http\Request $request
     * @param string $page
     * @return
     */
    public function back($request, $page);
}
