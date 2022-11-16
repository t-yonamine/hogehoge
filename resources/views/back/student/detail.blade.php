@extends('adminlte::page')

@section('title', '教習生詳細')

@section('content_header')
    <h1>教習生詳細</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-alert />
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <div class="m-2"><a class="btn btn-secondary float-right">編集</a></div>
                <x-forms.student.forms :infor="$infor" route="#"
                    method='GET' />
            </div>
        </div>
    </div>
@stop
