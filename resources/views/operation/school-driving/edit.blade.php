@extends('adminlte::page')

@section('title', '教習所詳細')

@section('content_header')
    <h1>教習所詳細</h1>
@stop

@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.school-driving :model="$model" route="{{ route('school-driving.edit') }}">
                </x-forms.school-driving>
            </div>
        </div>
    @stop
