<?php

namespace App\Dao\User;

use App\Contracts\Dao\User\UserDaoInterface;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => session('register-info.name'),
                'email' => session('register-info.email'),
                'password' => Hash::make(session('register-info.password')),
                'profile' => session('register-info.profile'),
                'type' => session('register-info.type'),
                'phone' => session('register-info.phone'),
                'address' => session('register-info.address'),
                'dob' => session('register-info.dob'),
                'create_user_id' => Auth::id(),
                'updated_user_id' => Auth::id(),
            ]);

            DB::commit();

        } catch (Exception $exception) {
            //Transaction failed
            DB::rollback();

            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $user,
        ];
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public function getAll()
    {
        //return User::all();
    }

    /**
     * Get User List.
     *
     * @return array $users
     */
    public function getList()
    {
        try {
            $users = User::whereNull('users.deleted_at')
                ->join('users AS u', 'users.create_user_id', '=', 'u.id')
                ->select('users.id', 'users.name', 'users.email', 'u.name AS created_user', 'users.phone', 'users.dob', 'users.address', 'users.created_at', 'users.updated_at')
                ->orderBy('users.id', 'asc')
                ->paginate(config('constants.pagination'));

        } catch (Exception $exception) {
            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $users,
        ];
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

        try {
            $users = User::whereNull('users.deleted_at')
                ->join('users AS u', 'users.create_user_id', '=', 'u.id')
                ->select('users.id', 'users.name', 'users.email', 'u.name AS created_user', 'users.phone', 'users.dob', 'users.address', 'users.created_at', 'users.updated_at')
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
            $users = $users->paginate(config('constants.pagination'));

        } catch (Exception $exception) {
            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $users,
        ];

    }

    /**
     * Get user by id.
     *
     * @param int $id
     * @return App\Models\User $users
     */
    public function getByID($id)
    {
        try {
            $user = User::find($id);
        } catch (Exception $exception) {
            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $user,
        ];
    }

    /**
     * Update the user information after the successful validation.
     *
     * @param App\Models\User $user
     * @return int
     */
    public function update(User $user)
    {
        try {
            DB::beginTransaction();

            $success = User::whereId($user->id)->update($user->toArray());

            DB::commit();

        } catch (Exception $exception) {
            DB::rollback();

            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $success,
        ];
    }

    /**
     * Make the user soft delete.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            //$user = User::destroy($id); //used soft delete but need to update 'deleted_user_id' manually
            $user = User::find($id);
            $user->deleted_user_id = Auth::id();
            $user->deleted_at = now();
            $user->save();

            DB::commit();

        } catch (Exception $exception) {
            DB::rollback();

            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $user,
        ];
    }

    /**
     * Change the user password.
     *
     * @param string $password
     * @return
     */
    public function changePassword($password)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $user->password = Hash::make($password);
            $user->save();

            DB::commit();

        } catch (Exception $exception) {
            DB::rollback();

            return [
                'status' => config('constants.exception')['code'],
                'message' => config('constants.exception')['message'],
                'data' => null,
            ];
        }

        return [
            'status' => config('constants.success')['code'],
            'message' => config('constants.success')['message'],
            'data' => $user,
        ];
    }

}
