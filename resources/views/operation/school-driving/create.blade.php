@extends('adminlte::page')

@section('title', '教習所登録')

@section('content_header')
    <h1>教習所登録</h1>
@stop

@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.school-driving :isCreate="true"  :model="$model" method='POST' route="{{ route('school-driving.store') }}">
                </x-forms.school-driving>
            </div>
        </div>
    @stop
