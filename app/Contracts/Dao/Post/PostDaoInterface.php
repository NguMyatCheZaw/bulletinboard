<?php

namespace App\Contracts\Dao\Post;

use App\Models\Post;

interface PostDaoInterface
{
    /**
     * Save to DB.
     *
     * @return App\Models\Post
     */
    public function create();

    /**
     * Get post list by all users.
     * When click search button with empty search box, all records will be shown.
     * If search with keyword, can be searched in post title and description by keyword.
     *
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyAll($searchKey);

    /**
     * Get post list by specific user.
     * When click search button with empty search box, all records will be shown.
     * If search with keyword, can be searched in post title and description by keyword.
     *
     * @param int $id
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyUser($id, $searchKey);

    /**
     * Get the post by id.
     *
     * @param int $id
     * @return App\Models\Post
     */
    public function getByID($id);

    /**
     * Make the post soft delete.
     *
     * @param int $id
     * @return
     */
    public function delete($id);

    /**
     * Update the specific post.
     *
     * @param int $id
     * @return App\Models\Post
     */
    public function update(Post $post);
}
