<?php

namespace App\Http\Controllers\Post;

use App\Contracts\Services\Post\PostServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Log;
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

        $posts = $this->getPost();

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
        log::info('$searchKey.....' . $searchKey);
        if (Auth::user()->type == 0) {
            $posts = $this->postServiceInterface->getListbyAll($searchKey);
        } else {
            $posts = $this->postServiceInterface->getListbyUser(Auth::id(), $searchKey);
        }

        return $posts;
    }

    /**
     * Search posts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        log::info('keyword.....' . $request->input('search'));
        $posts = $this->getPost($request->input('search'));

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
        log::info('prepareCreateForm');
        //clear the previous post information stored on session.
        $this->postServiceInterface->clear($request);
        log::info($request->session()->all());

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
            log::info($validator->errors());
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
        $this->postServiceInterface->create($request);
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
        log::info('prepareUpdateForm');
        $user = $this->postServiceInterface->prepareUpdateForm($request, $id);

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
            'title' => 'required|string|max:255|unique:posts,title,' . session('pid'),
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
        $this->postServiceInterface->update($request);

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
        $this->postServiceInterface->delete($id);
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
            //custom error messages
            $messages = [
                'mimetypes' => 'The file must be a file of type csv.',
            ];

            Validator::make($request->all(), [
                'csv-file' => 'required|file|max:1500|mimetypes:application/vnd.ms-excel|mimes:csv', //size is specified in kilobytes
            ], $messages)->validate();

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
