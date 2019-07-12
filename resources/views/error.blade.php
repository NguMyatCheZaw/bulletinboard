@extends('layouts.app')

@section('content')
<div class="container">
<h3 class="page-title text-center">Error Code: {{ $result['status'] }}</h3>
<div class="text-center"><b>Message</b>: {{ $result['message'] }}</div>
</div>
@endsection
