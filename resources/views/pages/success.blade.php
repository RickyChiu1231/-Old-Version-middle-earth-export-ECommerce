@extends('layouts.app')
@section('title', '操作成功')

@section('content')
  <div class="card">
    <div class="card-header">Success</div>
    <div class="card-body text-center">
      <h1>{{ $msg }}</h1>
      <a class="btn btn-primary" href="{{ route('root') }}">Back to Home</a>
    </div>
  </div>
@endsection
