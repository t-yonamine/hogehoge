@extends('adminlte::page')

@section('title', 'スタッフ詳細')

@section('content_header')
    <h1>スタッフ詳細</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.school-staff.form :data="$data" :user="$user" :isCreate="false"
                    route="{{ route('school-staff.update', [$data->id]) }}" method='PUT' />
            </div>
        </div>
    </div>
@stop
