@extends('adminlte::page')

@section('title', '運転適性検査登録')

@section('content_header')
    <h1>運転適性検査登録</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.aptitude-driving.form :testtype="$test_type" :data="$data" : method="POST" :seq="$seq"
                    route="{{ route('aptitude-driving.new', [$data->id]) }}" />
            </div>
        </div>
    </div>
@stop
