@extends('adminlte::page')

@section('title', '検定教習生登録')

@section('content_header')
    <h1>検定教習生登録</h1>
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
                <x-forms.apply-test.register-form :data="$data" route="{{ route('apply-test.create.save') }}"
                    method='POST' />
            </div>
        </div>
    </div>
@stop
