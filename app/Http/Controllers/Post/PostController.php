<?php

namespace App\Http\Controllers\Post;

use App\Contracts\Services\Post\PostServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Post Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the various operations on posts as well as their
    | validation.
    |
     */

    private $postServiceInterface;

    /**
     * Where to redirect users.
     *
     * @var string
     */
    protected $redirectTo = '/postlist';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostServiceInterface $postServiceInterface)
    {
        $this->middleware('auth');
        $this->postServiceInterface = $postServiceInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //delete the search keyword to be clear the search box.
        $request->session()->forget('search');

        $result = $this->getPost();

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        $posts = $result['data'];

        //flash on session to download the result.
        $request->session()->put('download-data', $posts);

        return view('post.postlist', compact('posts'));
    }

    /**
     * Get the post list by user type and search-keyword.
     *
     * @param string $searchKey
     * @return App\Models\Post $posts
     */
    public function getPost(string $searchKey = null)
    {
        if (Auth::user()->type == 0) {
            $result = $this->postServiceInterface->getListbyAll($searchKey);
        } else {
            $result = $this->postServiceInterface->getListbyUser(Auth::id(), $searchKey);
        }

        return $result;
    }

    /**
     * Search posts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $result = $this->getPost($request->input('search'));

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        $posts = $result['data'];

        //flash on session to show the search keyword in the search box.
        $request->session()->flash('search', $request->input('search'));

        //flash on session to download the searched result.
        $request->session()->put('download-data', $posts);

        return view('post.postlist', ['posts' => $posts]);
    }

    /**
     * Handle the old information on session and show the form for creating a new post.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function prepareCreateForm(Request $request)
    {
        //clear the previous post information stored on session.
        $this->postServiceInterface->clear($request, ['new-post']);

        return view('post.create');
    }

    /**
     * Validate the request and go to confirm page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:posts',
            'description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // store on session to show at confirm page
        // and if cancel, to be back to create form with input data.
        $this->postServiceInterface->createConfirm($request);

        return view('post.create_confirm');
    }

    /**
     * Store a newly created resource in DB.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = $this->postServiceInterface->create($request);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        return redirect($this->redirectTo);
    }

    /**
     * Handle the old information on session and show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function prepareUpdateForm(Request $request, $id)
    {
        $result = $this->postServiceInterface->prepareUpdateForm($request, $id);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        return view('post.update');
    }

    /**
     * Validate the request and go to confirm page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:posts,title,' . session('update-post.id'),
            'description' => 'required|string',
        ])->validate();

        // store on session to show at confirm page
        // and if cancel, to be back to update form with input data.
        $this->postServiceInterface->updateConfirm($request);

        return view('post.update_confirm');
    }

    /**
     * Update the specified resource in DB.
     *
     * @param \Illuminate\Http\Request $requests
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $result = $this->postServiceInterface->update($request);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        return redirect($this->redirectTo);
    }

    /**
     * Remove the specified resource from storage.
     * Soft delete post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->postServiceInterface->delete($id);

        if ($result['status'] == -9) {
            return view('error', compact('result'));
        }

        return redirect()->back();
    }

    /**
     * Upload the csv file and save file contents to db.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if ($request->file('csv-file')) {
            Validator::make($request->all(), [
                'csv-file' => 'required|file|max:1500', //size is specified in kilobytes
            ])->validate();

            $this->postServiceInterface->importCsv($request->file('csv-file'));
        }

        return redirect()->back();
    }

    /**
     * Download the excel file with the DB data.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return $this->postServiceInterface->exportExcel();
    }

}
