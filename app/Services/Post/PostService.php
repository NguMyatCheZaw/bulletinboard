<?php

namespace App\Services\Post;

use App\Contracts\Dao\Post\PostDaoInterface;
use App\Contracts\Services\Post\PostServiceInterface;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PostService implements PostServiceInterface
{
    private $postDao;

    /**
     * Class Constructor
     * @param OperatorPostDaoInterface
     * @return
     */
    public function __construct(PostDaoInterface $postDao)
    {
        $this->postDao = $postDao;
    }

    /**
     * Get post list by all users.
     *
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyAll($searchKey)
    {
        return $this->postDao->getListbyAll($searchKey);
    }

    /**
     * Get post list by user.
     *
     * @param int $id
     * @param string $searchKey
     * @return App\Models\Post
     */
    public function getListbyUser($id, $searchKey)
    {
        return $this->postDao->getListbyUser($id, $searchKey);
    }

    /**
     * Store the form input on session to show at confirm page.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function createConfirm($request)
    {
        // store on session to show at confirm page
        // and if cancel, to be back to create form with input data.
        $post = new Post;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $request->session()->put('new-post', $post);
    }

    /**
     * Save post to DB.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function create($request)
    {
        $result = $this->postDao->create();

        //clear the previous post information stored on session.
        $this->clear($request, ['new-post']);

        return $result;
    }

    /**
     * Handle the old information on session and show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return
     */
    public function prepareUpdateForm($request, $id)
    {
        //clear the previous post information stored on session.
        $this->clear($request, ['update-post']);

        $result = $this->postDao->getByID($id);

        //to show DB data in update form for edit
        $request->session()->put('update-post', $result['data']);

        return $result;
    }

    /**
     * Store the update info on session.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function updateConfirm($request)
    {
        // store on session to show at confirm page
        // and if cancel, to be back to update form with input data.
        $update = session('update-post');
        $update->title = $request->input('title');
        $update->description = $request->input('description');
        $update->status = $request->input('status');
        $request->session()->put('update-post', $update);
    }

    /**
     * Update the specific post on db.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function update($request)
    {
        $update = new Post;
        $update->id = session('update-post.id');
        $update->title = session('update-post.title');
        $update->description = session('update-post.description');
        if (session('update-post.status')) {
            $update->status = 1;
        } else {
            $update->status = 0;
        }
        $update->updated_user_id = Auth::id();

        $result = $this->postDao->update($update);

        //clear the previous user registration info stored on session.
        $this->clear($request, ['update-post']);

        return $result;
    }

    /**
     * Soft delete post.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        return $this->postDao->delete($id);
    }

    /**
     * Delete the old request info on session.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $attributeList
     * @return
     */
    public function clear($request, $attributeList)
    {
        foreach ($attributeList as $attribute) {
            $request->session()->forget($attribute);
        }
    }

    /**
     * Upload the csv file and save file contents to db.
     *
     * @param File $uploadedFile
     * @return
     */
    public function importCsv($uploadedFile)
    {
        //read the file, check validation and insert into db at once.
        Excel::import(new PostsImport, $uploadedFile);

        //save the uploaded file.
        //get just extension.
        $ext = $uploadedFile->getClientOriginalExtension();
        //make the file name.
        $filename = Auth::id() . '_' . date("Y-m-d_H-i-s") . '.' . $ext;
        //save the file.
        $uploadedFile->storeAs('public/upload_file', $filename);
    }

    /**
     * Download the excel file with the DB data.
     *
     * @return
     */
    public function exportExcel()
    {
        $posts = array();
        if (!empty(session('download-data'))) {
            //get the data stored on session.
            $download_data = session('download-data')->toArray()["data"];

            if (count($download_data) > 0) {
                foreach ($download_data as $data) {
                    //exclude the 'id' attribute.
                    $collection = collect($data);
                    $filtered = $collection->except(['id']);
                    //add to export array.
                    array_push($posts, $filtered->all());
                }
            }
        }

        //download the data.
        $export = new PostsExport($posts);
        return Excel::download($export, 'posts.xlsx');
    }

}
