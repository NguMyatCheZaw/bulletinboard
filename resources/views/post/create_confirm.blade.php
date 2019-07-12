@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('新規投稿追加の確認') }}</span></div>

                <div class="card-body">
                @if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
                    <form method="POST" action="{{ url('/post/create') }}">
                        @csrf

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('タイトル') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('new-post.title', '')}}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('内容') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('new-post.description', '')}}</label>
                            </div>
                        </div>

                        <div class="form-group row pt-4">
                            <div class="mx-auto">
                                <button type="submit" class="btn btn-primary mr-3 px-0" style="width: 100px">
                                    {{ __('登録') }}
                                </button>
                                <a class="btn btn-default px-0" href="{{ url('back') }}" style="width: 100px;">{{ __('キャンセル') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
