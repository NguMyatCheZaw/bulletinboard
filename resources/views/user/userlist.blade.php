@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('ユーザー一覧') }}</span></div>

                <div class="card-body">
                    <form method="GET" action="{{ route('user.search') }}">
                        @csrf

                        <div class="container">
                            <div class="">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="">
                                @error('createdfrom')
                                    <span class="invalid-feedback" role="alert" style="display:block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-5">
                                <input id="name" type="text" class="col-10 col-sm-2 mb-2 pl-1 @error('name') is-invalid @enderror" name="name" value="{{ old('name', session('name-search', '')) }}" autocomplete="name" placeholder="名">
                                <input id="email" type="text" class="col-10 col-sm-2 mb-2 pl-1 @error('email') is-invalid @enderror" name="email" value="{{ old('email', session('email-search', '')) }}" autocomplete="email" placeholder="メール">
                                <input id="createdfrom" type="text" class="datepick col-10 col-sm-2 mb-2 pl-1 @error('createdfrom') is-invalid @enderror" name="createdfrom" value="{{ old('createdfrom', session('from-search', '')) }}" autocomplete="createdfrom" placeholder="作成日(から)">
                                <input id="createdto" type="text" class="datepick col-10 col-sm-2 mb-2 pl-1 @error('createdto') is-invalid @enderror" name="createdto" value="{{ old('createdto', session('to-search', '')) }}" autocomplete="createdto" placeholder="作成日(に)">
                                <button type="submit" class="btn btn-primary rounded-0" style="width: 100px">
                                    {{ __('検索') }}
                                </button>
                            </div>
                        </div>

                        <div class="container">
                            <div class="mt-5 mb-2">
                                <a class="btn btn-primary rounded-0 my-2 px-2" href="{{ route('user.reg.index') }}" style="width: 100px;">{{ __('追加') }}</a>
                            </div>
                            @if ($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th class="align-middle">ユーザー名</th>
                                            <th class="align-middle">メールアドレス</th>
                                            <th class="align-middle">作成者</th>
                                            <th class="align-middle">電話番号</th>
                                            <th class="align-middle">誕生日</th>
                                            <th class="align-middle">住所</th>
                                            <th class="align-middle">作成日</th>
                                            <th class="align-middle">更新日</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                        <tr>
                                            <td><a href="{{ route('user.upd.index', ['id'=>$user->id]) }}">{{ $user->name }}</a></td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_user }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->dob }}</td>
                                            <td>{{ $user->address }}</td>
                                            <td>{{ date('Y-m-d', strtotime($user->created_at)) }}</td>
                                            <td>{{ date('Y-m-d', strtotime($user->updated_at)) }}</td>
                                            <td class="text-center"><a href="#deleteModal" class="" data-toggle="modal" data-id="{{ $user->id }}">削除</a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $users->appends(request()->except('page'))->links() }}
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="deleteModal" class="modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <!-- Modal content-->
        <form action="" id="deleteForm" method="POST">
            <div class="modal-content">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title">確認</h4>
                    <button type="button" class="close my-0 py-0" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="text-center">消去してもよろしいですか？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('キャンセル') }}</button>
                    <button type="submit" class="btn btn-warning" data-dismiss="modal" onclick="formSubmit()">{{ __('はい') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#deleteModal').on('show.bs.modal', function (event) {
        var element = $(event.relatedTarget) // Button that triggered the modal
        var id = element.data('id') // Extract info from data-* attributes

        var modal = $(this)
        var url = '{{ route("user.delete", ":id") }}';
        url = url.replace(':id', id);

        modal.find('#deleteForm').attr('action', url)
    });
    function formSubmit() {
        $("#deleteForm").submit();
    }
</script>
@endsection
