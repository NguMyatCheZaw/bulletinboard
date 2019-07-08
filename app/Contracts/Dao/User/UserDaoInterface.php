<?php

namespace App\Contracts\Dao\User;

use App\Models\User;
use Illuminate\Http\Request;

interface UserDaoInterface
{
    public function create();
    public function getAll();
    public function getList();
    public function search(Request $request);
    public function getByID($id);
    public function update(User $user);
    public function delete($id);
    public function changePassword($password);
}
