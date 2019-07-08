<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

/* ***********************************User Routes*********************************** */
Route::get('/user/register/index', 'User\UserController@prepareRegisterForm')->name('user.reg.index');
Route::post('/user/register/confirm', 'User\UserController@registerConfirm')->name('user.reg.confirm');
Route::post('/user/register', 'User\UserController@create')->name('user.create');

Route::get('/user/update/index/{id}', 'User\UserController@prepareUpdateForm')->name('user.upd.index');
Route::post('/user/update/confirm', 'User\UserController@updateConfirm')->name('user.upd.confirm');
Route::post('/user/update', 'User\UserController@update')->name('user.update');
Route::get('/user/update', function () {
    return view('user.update');
})->name('updateform');

Route::get('/userlist', 'User\UserController@getList');
Route::get('/user/search', 'User\UserController@search')->name('user.search');

Route::get('/password/change', function () {
    return view('user.change_password');
});
Route::post('/changepwd', 'User\UserController@changePassword')->name('password.change');

Route::post('/user/delete/{id}', 'User\UserController@delete')->name('user.delete');

Route::get('/profile/{id}', 'User\UserController@showProfile');

Route::get('/back/{page}', 'User\UserController@back');

/* ***********************************Post Routes*********************************** */
Route::get('/post/create/index', 'Post\PostController@prepareCreateForm')->name('post.create.index');
Route::post('/post/create/confirm', 'Post\PostController@createConfirm')->name('post.create.confirm');
Route::post('/post/create', 'Post\PostController@store');

Route::get('/post/update/index/{id}', 'Post\PostController@prepareUpdateForm')->name('post.upd.index');
Route::post('/post/update/confirm', 'Post\PostController@updateConfirm')->name('post.upd.confirm');
Route::post('/post/update', 'Post\PostController@update')->name('post.update');

Route::get('/postlist', 'Post\PostController@index');
Route::post('/post/search', 'Post\PostController@search')->name('post.search');

Route::post('/post/delete/{id}', 'Post\PostController@destroy')->name('post.delete');

Route::get('/back', function () {
    return view('post.create');
});
Route::get('/back/post/update', function () {
    return view('post.update');
});

//----------------------routes for upload/download
Route::get('/post/upload/index', function () {
    return view('post.uploadcsv');
})->name('post.upload.index');
Route::post('/post/upload', 'Post\PostController@import')->name('post.upload');
Route::get('/post/download', 'Post\PostController@export')->name('post.download');
