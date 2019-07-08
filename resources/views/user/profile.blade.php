@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('プロフィール') }}</span></div>

                <div class="card-body">

                        <div class="form-group row">
                            <div class="col-md-4"></div>
                            <div class="col-md-6 d-flex justify-content-end">
                                <a href="{{ route('user.upd.index', ['id'=>$user->id]) }}"><u>{{ __('編集') }}</u></a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('ユーザー名') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ $user->name }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('メールアドレス') }}</label>

                            <div class="col-md-6 bg-light py-2">
                                <a href="#">{{ $user->email }}</a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('ユーザー役割') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ $user->type ? 'メンバー' : '管理者' }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('電話番号') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ $user->phone }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('誕生日') }}</label>

                            <div class="col-md-6 bg-light">
                                <label class="col-form-label">{{ $user->dob }}</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('住所') }}</label>

                            <div class="col-md-6 bg-light">
                                <address class="col-form-label">{{ $user->address }}</address>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
