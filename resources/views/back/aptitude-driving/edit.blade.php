@extends('adminlte::page')

@section('title', '運転適性検査編集')

@section('content_header')
    <h1>運転適性検査編集</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.aptitude-driving.form :isCreate="false" :model="$model" method="POST"
                    :data="$data" route="{{ route('aptitude-driving.edit', [$data->id]) }}" />
            </div>
        </div>
    </div>
@stop
