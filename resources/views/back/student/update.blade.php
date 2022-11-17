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
                <x-forms.student.forms action="edit" :infor="$infor" route="{{route('student.update', $infor->admCheckItem->ledger_id)}}"
                    method='POST' />
            </div>
        </div>
    </div>
@stop
