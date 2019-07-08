@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('ユーザー情報更新の確認') }}</span></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('user.update') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-5 offset-4 offset-sm-5 offset-lg-7 mb-3">
                            @if (session()->has('new-profile'))
                                <figure><img class="rounded" src="{{ asset('storage/image/'.session('new-profile')) }}" width="150px" height="150px" /></figure>
                            @else
                                <figure><img class="rounded" src="{{ asset('storage/image/'.session('profile')) }}" width="150px" height="150px" /></figure>
                            @endif
                            </div>

                            <div class="w-100"></div>

                            <label class="col-md-4 col-form-label text-md-right">{{ __('ユーザー名') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('name') }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('メールアドレス') }}</label>

                            <div class="col-md-6 bg-light py-2">
                                <a href="#">{{ session('email') }}</a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('ユーザー役割') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('type') ? 'メンバー' : '管理者' }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('電話番号') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('phone') }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('誕生日') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ session('dob') }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('住所') }}</label>

                            <div class="col-md-6 bg-light">
                                <address class="col-form-label">{{ session('address') }}</address>
                            </div>
                        </div>

                        <div class="form-group row pt-4">
                            <div class="mx-auto">
                                <button type="submit" class="btn btn-primary mr-3 px-0" style="width: 100px">
                                    {{ __('更新') }}
                                </button>
                                <a class="btn btn-default px-0" href="{{ url('back/updateform') }}" style="width: 100px;">{{ __('キャンセル') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
