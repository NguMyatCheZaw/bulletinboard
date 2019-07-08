<?php

namespace App\Dao\Post;

use App\Contracts\Dao\Post\PostDaoInterface;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class PostDao implements PostDaoInterface
{

    /**
     * Save to DB after validations success.
     *
     * @return App\Models\Post
     */
    public function create()
    {
        $post = Post::create([
            'title' => session('title'),
            'description' => session('description'),
            'create_user_id' => Auth::id(),
            'updated_user_id' => Auth::id(),
        ]);
        log::info('created post:');
        log::info($post);
        return $post;
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
        $posts = DB::table('posts')
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
            ->paginate(5);

        return $posts;
    }

    /**
     * Get post list by specific user.
     * When click search button with empty search box, all records will be shown.
     * If search with keyword, can be searched by keyword in post title and description.
     *
     * @param int $id
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyUser($id, $searchKey)
    {
        $posts = DB::table('posts')
            ->join('users', 'posts.create_user_id', '=', 'users.id')
            ->select('posts.id', 'posts.title', 'posts.description', 'posts.status', 'users.name AS posted_user', 'posts.created_at AS posted_date', 'posts.updated_at AS updated_date')
            ->when(!empty($searchKey), function ($query) use ($searchKey) {
                return $query->where(function ($query) use ($searchKey) {
                    return $query->where('posts.title', 'LIKE', '%' . $searchKey . '%')
                        ->orWhere('posts.description', 'LIKE', '%' . $searchKey . '%');
                });
            })
            ->where('posts.create_user_id', $id)
        /* ->where('posts.status', 1) */
            ->whereNull('posts.deleted_at')
            ->orderBy('posts.id', 'asc')
            ->paginate(5);

        return $posts;
    }

    /**
     * Get the post by id.
     *
     * @param int $id
     * @return App\Models\Post
     */
    public function getById($id)
    {
        return Post::find($id);
    }

    /**
     * Make the post soft delete.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        //Post::destroy($id); //used soft delete but needed to update 'deleted_user_id' manually
        $post = Post::find($id);
        $post->deleted_user_id = Auth::id();
        $post->deleted_at = now();
        $post->save();
    }

    /**
     * Update the specific post.
     *
     * @param int $id
     * @return App\Models\Post
     */
    public function update(Post $post)
    {
        return Post::whereId($post->id)->update($post->toArray());
    }
}
