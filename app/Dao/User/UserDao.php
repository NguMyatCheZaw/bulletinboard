<?php

namespace App\Dao\User;

use App\Contracts\Dao\User\UserDaoInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Log;

class UserDao implements UserDaoInterface
{

    /**
     * Create a new user after a valid registration.
     *
     * @param
     * @return App\Models\User $user
     */
    public function create()
    {
        $user = User::create([
            'name' => session('name'),
            'email' => session('email'),
            'password' => Hash::make(session('password')),
            'profile' => session('profile'),
            'type' => session('type'),
            'phone' => session('phone'),
            'address' => session('address'),
            'dob' => session('dob'),
            'create_user_id' => Auth::id(),
            'updated_user_id' => Auth::id(),
        ]);

        return $user;
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public function getAll()
    {
        return User::all();
    }

    /**
     * Get User List.
     *
     * @return array $users
     */
    public function getList()
    {
        $users = DB::table('users')
            ->join('users AS u', 'users.create_user_id', '=', 'u.id')
            ->select('users.id', 'users.name', 'users.email', 'u.name AS created_user', 'users.phone', 'users.dob', 'users.address', 'users.created_at', 'users.updated_at')
            ->whereNull('users.deleted_at')
            ->orderBy('users.id', 'asc')
            ->paginate(5);

        return $users;
    }

    /**
     * Get users by search conditions.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\User $users
     */
    public function search(Request $request)
    {
        $bothfilled = false;
        $fromfilled = false;
        $tofilled = false;
        if ($request->filled('createdfrom') && $request->filled('createdto')) {
            $bothfilled = true;
        } else {
            if ($request->filled('createdfrom')) {
                $fromfilled = true;
            } elseif ($request->filled('createdto')) {
                $tofilled = true;
            }
        }

        $users = DB::table('users')
            ->join('users AS u', 'users.create_user_id', '=', 'u.id')
            ->select('users.id', 'users.name', 'users.email', 'u.name AS created_user', 'users.phone', 'users.dob', 'users.address', 'users.created_at', 'users.updated_at')
            ->whereNull('users.deleted_at')
            ->where(function ($query) use ($request, $bothfilled, $fromfilled, $tofilled) {
                $query->when($request->filled('name'), function ($query) use ($request) {
                    return $query->orWhere('users.name', 'LIKE', '%' . $request->input('name') . '%');
                });
                $query->when($request->filled('email'), function ($query) use ($request) {
                    return $query->orWhere('users.email', 'LIKE', '%' . $request->input('email') . '%');
                });
                $query->when($bothfilled, function ($query) use ($request) {
                    return $query->orWhereBetween('users.created_at', [$request->input('createdfrom'), $request->input('createdto')]);
                });
                $query->when($fromfilled, function ($query) use ($request) {
                    return $query->orWhere('users.created_at', '>=', $request->input('createdfrom'));
                });
                $query->when($tofilled, function ($query) use ($request) {
                    return $query->orWhere('users.created_at', '<=', $request->input('createdto'));
                });
            });

        $users->orderBy('users.id', 'asc');
        $users = $users->paginate(5);
        log::info('search query***');

        return $users;
    }

    /**
     * Get user by id.
     *
     * @param int $id
     * @return App\Models\User $users
     */
    public function getByID($id)
    {
        return User::find($id);
    }

    /**
     * Update the user information after the successful validation.
     *
     * @param App\Models\User $user
     * @return int
     */
    public function update(User $user)
    {
        return User::whereId($user->id)->update($user->toArray());
    }

    /**
     * Make the user soft delete.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        //$user = User::destroy($id); //used soft delete but need to update 'deleted_user_id' manually
        $user = User::find($id);
        $user->deleted_user_id = Auth::id();
        $user->deleted_at = now();
        $user->save();
    }

    /**
     * Change the user password.
     *
     * @param string $password
     * @return
     */
    public function changePassword($password)
    {
        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();
    }

}
