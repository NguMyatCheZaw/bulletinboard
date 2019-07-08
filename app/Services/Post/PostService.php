<?php

namespace App\Services\Post;

use App\Contracts\Dao\Post\PostDaoInterface;
use App\Contracts\Services\Post\PostServiceInterface;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
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
    public function createConfirm(Request $request)
    {
        // store on session to show at confirm page
        // and if cancel, to be back to create form with input data.
        $request->session()->put('title', $request->input('title'));
        $request->session()->put('description', $request->input('description'));
        log::info(session()->all());
    }

    /**
     * Save post to DB.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function create(Request $request)
    {
        $this->postDao->create();

        //clear the previous post information stored on session.
        $this->clear($request);
        log::info(session()->all());
    }

    /**
     * Handle the old information on session and show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int  $id
     * @return
     */
    public function prepareUpdateForm(Request $request, $id)
    {
        //clear the previous post information stored on session.
        $this->clear($request);

        $user = $this->postDao->getByID($id);

        //to show DB data in update form for edit
        $request->session()->put('pid', $user->id);
        $request->session()->put('title', $user->title);
        $request->session()->put('description', $user->description);
        $request->session()->put('pstatus', $user->status);
        log::info(session()->all());
    }

    /**
     * Validate the request and go to confirm page.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function updateConfirm(Request $request)
    {
        // store on session to show at confirm page
        // and if cancel, to be back to update form with input data.
        $request->session()->put('title', $request->input('title'));
        $request->session()->put('description', $request->input('description'));
        $request->session()->put('pstatus', $request->input('status'));
        log::info(session()->all());

    }

    /**
     * Update the specific post on db.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function update(Request $request)
    {
        $update = new Post;
        $update->id = session('pid');
        $update->title = session('title');
        $update->description = session('description');
        if (session('pstatus')) {
            $update->status = 1;
        } else {
            $update->status = 0;
        }
        $update->updated_user_id = Auth::id();

        $this->postDao->update($update);

        //clear the previous user registration info stored on session.
        $this->clear($request);
        log::info($request->session()->all());
    }

    /**
     * Soft delete post.
     *
     * @param int $id
     * @return
     */
    public function delete($id)
    {
        $this->postDao->delete($id);
    }

    /**
     * Delete the old request info on session.
     *
     * @param \Illuminate\Http\Request $request
     * @return
     */
    public function clear(Request $request)
    {
        $request->session()->forget('pid');
        $request->session()->forget('title');
        $request->session()->forget('description');
        $request->session()->forget('pstatus');
    }

    /**
     * Upload the csv file and save file contents to db.
     *
     * @param $uploadedFile
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
