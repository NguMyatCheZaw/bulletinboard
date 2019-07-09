@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('ＣＳＶファイルアップロード') }}</span></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('post.upload') }}"  enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-6 mx-auto">
                                @if ($errors->any())
                                    <div>
                                        @foreach ($errors->all() as $error)
                                        <span class="invalid-feedback" role="alert" style="display:block;">
                                            <strong>{{ $error }}</strong>
                                        </span>
                                        @endforeach
                                    </div>
                                @endif
                                <input id="csv" type="file" class="form-control pb-5" name="csv-file">
                            </div>
                        </div>

                        <div class="form-group row pt-2">
                            <div class="mx-auto">
                                <button type="submit" class="btn btn-primary mr-3 px-0" style="width: 100px">
                                    {{ __('インポート') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
