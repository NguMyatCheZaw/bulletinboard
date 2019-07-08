@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><span style="font-size: 16px">{{ __('投稿一覧') }}</span></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('post.search') }}">
                        @csrf

                        <div class="container">
                            <div class="my-2">
                                <input id="search" type="text" class="col-6 col-sm-4 mb-2 pl-1" name="search" value="{{ session('search', '') }}">
                                <button type="submit" class="btn btn-primary rounded-0 my-2" style="width: 100px">
                                    {{ __('検索') }}
                                </button>
                            </div>
                        </div>

                        <div class="container">
                            <div class="mt-5 mb-2">
                                <a class="btn btn-primary rounded-0 my-2 px-2" href="{{ route('post.create.index') }}" style="width: 100px;">{{ __('追加') }}</a>
                                <a class="btn btn-primary rounded-0 my-2 px-2" href="{{ route('post.upload.index') }}" style="width: 100px;">{{ __('アップロード') }}</a>
                                <a class="btn btn-primary rounded-0 my-2 px-2" href="{{ route('post.download') }}" style="width: 100px;">{{ __('ダウンロード') }}</a>
                            </div>
                            @if ($posts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th class="align-middle">タイトル</th>
                                            <th class="align-middle">内容</th>
                                            <th class="align-middle">作成者</th>
                                            <th class="align-middle">作成日</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($posts as $post)
                                        <tr>
                                            <td>
                                                <a href="#detailModal" data-toggle="modal" data-title="{{ $post->title }}"
                                                    data-desc="{{ $post->description }}" data-status="{{ $post->status }}"
                                                    data-posteduser="{{ $post->posted_user }}" data-posteddate="{{ date('Y-m-d', strtotime($post->posted_date)) }}"
                                                    data-updateddate="{{ date('Y-m-d', strtotime($post->updated_date)) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </td>
                                            <td>{{ $post->description }}</td>
                                            <td>{{ $post->posted_user }}</td>
                                            <td>{{ date('Y-m-d', strtotime($post->posted_date)) }}</td>
                                            <td class="text-center"><a href="{{ route('post.upd.index', $post->id) }}">編集</a></td>
                                            <td class="text-center"><a href="#deleteModal" class="" data-toggle="modal" data-id="{{ $post->id }}">削除</a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $posts->appends(request()->except('page'))->links() }}
                            </div>
                            @else
                            <div class="row">
                                <div class="col text-center"><span>There is no post to show.</span></div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Display the desired post on modal. -->
<div id="detailModal" class="modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">投稿詳細</h4>
                <button type="button" class="close my-0 py-0" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{ __('タイトル') }}</label>

                    <div class="col-md-6 bg-light">
                        <label id="title" class="col-form-label"></label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{ __('内容') }}</label>

                    <div class="col-md-6 bg-light">
                        <label id="description" class="col-form-label"></label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-6 col-sm-4 col-form-label text-md-right">{{ __('ステータス') }}</label>

                    <div class="col-6 px-md-0">
                        <input id="status" class="align-bottom" type="checkbox" id="status" disabled>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{ __('作成者') }}</label>

                    <div class="col-md-6 bg-light">
                        <label id="posted_user" class="col-form-label"></label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-4 col-form-label text-md-right">{{ __('作成日') }}</label>

                    <div class="col-6 bg-light">
                        <label id="posted_date" class="col-form-label"></label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-4 col-form-label text-md-right">{{ __('更新日') }}</label>

                    <div class="col-6 bg-light">
                        <label id="updated_date" class="col-form-label"></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('OK') }}</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#detailModal').on('show.bs.modal', function (event) {
        var element = $(event.relatedTarget) // Button that triggered the modal
        var title = element.data('title') // Extract info from data-* attributes
        var desc = element.data('desc')
        var status = element.data('status')
        var posteduser = element.data('posteduser')
        var posteddate = element.data('posteddate')
        var updateddate = element.data('updateddate')

        var modal = $(this)
        modal.find('.modal-body #title').text(title)
        modal.find('.modal-body #description').text(desc);
        modal.find('.modal-body #status').prop("checked", status);
        modal.find('.modal-body #posted_user').text(posteduser);
        modal.find('.modal-body #posted_date').text(posteddate);
        modal.find('.modal-body #updated_date').text(updateddate);
    });
</script>

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
        var url = '{{ route("post.delete", ":id") }}';
        url = url.replace(':id', id);

        modal.find('#deleteForm').attr('action', url)
    });
    function formSubmit() {
        $("#deleteForm").submit();
    }
</script>
@endsection
