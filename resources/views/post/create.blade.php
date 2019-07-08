@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('新規投稿') }}</span></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('post.create.confirm') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('タイトル') }}</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', session('title', '')) }}" autocomplete="title" autofocus>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('内容') }}</label>

                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows=4>{{ old('description', session('description', '')) }}</textarea>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

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

<!-- form reset -->
<script type="text/javascript">
    $("#reset").on("click", function () {
        $("#title").val(null);
        $("#description").val(null);
    });
</script>
@endsection
