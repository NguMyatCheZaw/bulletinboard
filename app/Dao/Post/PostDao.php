<?php

namespace App\Dao\Post;

use App\Contracts\Dao\Post\PostDaoInterface;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostDao implements PostDaoInterface
{

    /**
     * Save to DB.
     *
     * @return App\Models\Post
     */
    public function create()
    {
        try {
            DB::beginTransaction();

            $post = Post::create([
                'title' => session('new-post.title'),
                'description' => session('new-post.description'),
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
            'data' => $post,
        ];
    }

    /**
     * Get post list by all users.
     * When click search button with empty search box, all records will be shown.
     * If search with keyword, can be searched in post title and description by keyword.
     *
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyAll($searchKey)
    {
        try {
            $posts = Post::whereNull('posts.deleted_at')
                ->join('users', 'posts.create_user_id', '=', 'users.id')
                ->select('posts.id', 'posts.title', 'posts.description', 'posts.status', 'users.name AS posted_user', 'posts.created_at AS posted_date', 'posts.updated_at AS updated_date')
                ->when(!empty($searchKey), function ($query) use ($searchKey) {
                    return $query->where(function ($query) use ($searchKey) {
                        return $query->where('posts.title', 'LIKE', '%' . $searchKey . '%')
                            ->orWhere('posts.description', 'LIKE', '%' . $searchKey . '%');
                    });
                })
            /* ->where('posts.status', 1) */
                ->orderBy('posts.id', 'asc')
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
            'data' => $posts,
        ];
    }

    /**
     * Get post list by specific user.
     * When click search button with empty search box, all records will be shown.
     * If search with keyword, can be searched in post title and description by keyword.
     *
     * @param int $id
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyUser($id, $searchKey)
    {
        try {
            $posts = Post::where('posts.create_user_id', $id)
                ->join('users', 'posts.create_user_id', '=', 'users.id')
                ->select('posts.id', 'posts.title', 'posts.description', 'posts.status', 'users.name AS posted_user', 'posts.created_at AS posted_date', 'posts.updated_at AS updated_date')
                ->when(!empty($searchKey), function ($query) use ($searchKey) {
                    return $query->where(function ($query) use ($searchKey) {
                        return $query->where('posts.title', 'LIKE', '%' . $searchKey . '%')
                            ->orWhere('posts.description', 'LIKE', '%' . $searchKey . '%');
                    });
                })
            /* ->where('posts.status', 1) */
                ->whereNull('posts.deleted_at')
                ->orderBy('posts.id', 'asc')
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
            'data' => $posts,
        ];
    }

    /**
     * Get the post by id.
     *
     * @param int $id
     * @return App\Models\Post
     */
    public function getById($id)
    {
        try {
            $post = Post::find($id);
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
            'data' => $post,
        ];
    }

    /**
     * Make the post soft delete.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            //Post::destroy($id); //used soft delete but needed to update 'deleted_user_id' manually
            $post = Post::find($id);
            $post->deleted_user_id = Auth::id();
            $post->deleted_at = now();
            $post->save();

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
            'data' => $post,
        ];
    }

    /**
     * Update the specific post.
     *
     * @param int $id
     * @return App\Models\Post
     */
    public function update(Post $post)
    {
        try {
            DB::beginTransaction();

            $success = Post::whereId($post->id)->update($post->toArray());

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
}
