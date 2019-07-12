<?php

namespace App\Contracts\Dao\User;

use App\Models\User;
use Illuminate\Http\Request;

interface UserDaoInterface
{
    /**
     * store user
     *
     * @param
     * @return App\Models\User
     */
    public function create();

    /**
     * get all users
     *
     * @param
     * @return array
     */
    public function getAll();

    /**
     * get the user list
     *
     * @param
     * @return array
     */
    public function getList();

    /**
     * get users by search keywords
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function search(Request $request);

    /**
     * get user by id
     *
     * @param int $id
     * @return App\Models\User
     */
    public function getByID($id);

    /**
     * update the user
     *
     * @param App\Models\User $user
     * @return int
     */
    public function update(User $user);

    /**
     * soft delete
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
}
