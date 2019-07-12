@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('投稿更新の確認') }}</span></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('post.update') }}">
                        @csrf

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('タイトル') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('update-post.title') }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('内容') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('update-post.description') }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-4 col-form-label text-md-right">{{ __('ステータス') }}</label>

                            <div class="col-6 px-md-0">
                                <input class="align-bottom" type="checkbox" name="status" id="status" {{ session('update-post.status') ? 'checked' : '' }} disabled>
                            </div>
                        </div>

                        <div class="form-group row pt-4">
                            <div class="mx-auto">
                                <button type="submit" class="btn btn-primary mr-3 px-0" style="width: 100px">
                                    {{ __('更新') }}
                                </button>
                                <a class="btn btn-default px-0" href="{{ url('/back/post/update') }}" style="width: 100px;">{{ __('キャンセル') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
