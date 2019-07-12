<?php

namespace App\Contracts\Services\Post;

use App\Models\Post;
use Illuminate\Http\Request;

interface PostServiceInterface
{
    /**
     * get post list by all users
     *
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyAll($searchKey);

    /**
     * get post list by user
     *
     * @param int $id
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyUser($id, $searchKey);

    /**
     * store the form input on session to show at confirm page
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function createConfirm($request);

    /**
     * save post to DB
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function create($request);

    /**
     * handle the old information on session and show the form for editing the specified resource
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return
     */
    public function prepareUpdateForm($request, $id);

    /**
     * store the update info on session
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function updateConfirm($request);

    /**
     * update the post on db
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function update($request);

    /**
     * soft delete
     *
     * @param int $id
     * @return
     */
    public function delete($id);

    /**
     * delete the old request info on session
     *
     * @param \Illuminate\Http\Request $request
     * @param array $attributeList
     * @return
     */
    public function clear($request, $attributeList);

    /**
     * upload the csv file and save file contents to db
     *
     * @param File $uploadedFile
     * @return
     */
    public function importCsv($uploadedFile);

    /**
     * download the excel file with the DB data
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function exportExcel();
}
