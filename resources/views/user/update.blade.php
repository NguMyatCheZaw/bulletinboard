@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('ユーザー情報更新') }}</span></div>

                <div class="card-body">
                    <form id="form" method="POST" action="{{ route('user.upd.confirm') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <div class="col-5 offset-4 offset-sm-5 offset-lg-7 mb-3">
                                <figure><img class="rounded" src="{{ asset('storage/image/'.session('profile')) }}" width="150px" height="150px" /></figure>
                            </div>

                            <div class="w-100"></div>

                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('ユーザー名') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', session('name')) }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('メールアドレス') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', session('email')) }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('ユーザー役割') }}</label>

                            <div class="col-md-6">
                                <select id="type" class="form-control @error('type') is-invalid @enderror" name="type">
                                    <option class="admin" value="0" {{ old('type', session('type')) ? '' : 'selected' }}>管理者</option>
                                    <option value="1" {{ old('type', session('type')) ? 'selected' : '' }}>メンバー</option>
                                </select>

                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('電話番号') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', session('phone')) }}" required autocomplete="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="dob" class="col-md-4 col-form-label text-md-right">{{ __('誕生日') }}</label>

                            <div class="col-md-6">
                                <input id="dob" type="text" class="datepick form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob', session('dob')) }}" autocomplete="dob">

                                @error('dob')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('住所') }}</label>

                            <div class="col-md-6">
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" rows=4>{{ old('address', session('address')) }}</textarea>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="filename" class="col-md-4 col-form-label text-md-right">{{ __('プロフィール') }}</label>

                            <div class="col-md-6">
                                <input type="text" id="filename" class="border text-secondary" style="border-color: #ced4da;" placeholder="選択されていません" readonly />

                                <label for="profile" class="border border-secondary" style="background-color: #e1e1e1;">
                                    ファイルを選択
                                    <input type="file" id="profile" name="profile" style="display:none;">
                                </label>

                                @error('profile')
                                    <span class="invalid-feedback" role="alert" style="display:block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @auth
                            @if (Auth::user()->type == 1)
                                <div class="form-group row">
                                    <div class="col-md-4 text-md-right">
                                        <a class="" href="/password/change">{{ __('パスワードの変更') }}</a>
                                    </div>
                                </div>
                            @endif
                        @endauth
                        <div class="form-group row pt-4">
                            <div class="mx-auto">
                                <button type="submit" class="btn btn-primary mr-3 px-0" style="width: 100px">
                                    {{ __('確認') }}
                                </button>
                                <button type="button" id="reset" class="btn btn-default px-0" style="width: 100px">
                                    {{ __('クリア') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Custom input type=file -->
<script type="text/javascript">
    $(function() {
        $('#profile').change(function() {
            var val = $(this).val();
            var path = val.replace(/\\/g, '/');
            var match = path.lastIndexOf('/');
            $('#filename').css("display","inline-block");
            $('#filename').val(match !== -1 ? val.substring(match + 1) : val);
        });
        $('#filename').bind('keyup, keydown, keypress', function() {
            return false;
        });
    });
</script>

<!-- form reset -->
<script type="text/javascript">
    $("#reset").on("click", function () {
        $("input:text").val(null);
        $("#email").val(null);
        $("#address").val(null);
        $(".admin").prop('selected', 'selected');
    });
</script>
@endsection
