<?php

namespace App\Contracts\Services\Post;

use App\Models\Post;
use Illuminate\Http\Request;

interface PostServiceInterface
{
    public function getListbyAll($searchKey);
    public function getListbyUser($id, $searchKey);
    public function createConfirm(Request $request);
    public function create(Request $request);
    public function prepareUpdateForm(Request $request, $id);
    public function updateConfirm(Request $request);
    public function update(Request $request);
    // public function getByID($id);
    public function delete($id);
    public function clear(Request $request);
    public function importCsv($uploadedFile);
    public function exportExcel();
}
