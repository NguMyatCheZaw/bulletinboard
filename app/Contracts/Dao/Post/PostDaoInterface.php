<?php

namespace App\Contracts\Dao\Post;

use App\Models\Post;

interface PostDaoInterface
{
    public function create();
    public function getListbyAll($searchKey);
    public function getListbyUser($id, $searchKey);
    public function getByID($id);
    public function delete($id);
    public function update(Post $post);
}
